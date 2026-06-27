<?php

namespace App\Services\WhatsApp;

use App\Services\WhatsApp\Contracts\WhatsAppGateway;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class EvolutionApiGateway implements WhatsAppGateway
{
    public function __construct(
        private readonly string $baseUrl,
        private readonly string $apiKey,
        private readonly string $instancia,
        private readonly int $timeout = 30,
    ) {
    }

    public function configurado(): bool
    {
        return filled($this->baseUrl) && filled($this->apiKey) && filled($this->instancia);
    }

    /**
     * @return array{state: ?string, conectada: bool, raw: mixed}
     */
    public function estadoConexao(): array
    {
        if (! $this->configurado()) {
            return ['state' => null, 'conectada' => false, 'raw' => null];
        }

        try {
            $resposta = Http::timeout($this->timeout)
                ->withHeaders(['apikey' => $this->apiKey])
                ->acceptJson()
                ->get(rtrim($this->baseUrl, '/').'/instance/connectionState/'.$this->instanciaCodificada());

            if ($resposta->failed()) {
                return ['state' => null, 'conectada' => false, 'raw' => $resposta->json()];
            }

            $corpo = $resposta->json();
            $state = is_array($corpo)
                ? ($corpo['instance']['state'] ?? $corpo['state'] ?? null)
                : null;

            return [
                'state' => is_string($state) ? $state : null,
                'conectada' => $state === 'open',
                'raw' => $corpo,
            ];
        } catch (\Throwable $e) {
            return ['state' => null, 'conectada' => false, 'raw' => $e->getMessage()];
        }
    }

    public function enviarTexto(string $numero, string $texto): void
    {
        $this->garantirConexaoAberta();
        $this->enviarComVariacoes($numero, fn (string $destino) => $this->tentarEnvioTexto($destino, $texto));
    }

    public function enviarImagem(string $numero, string $url, string $legenda = ''): void
    {
        $this->garantirConexaoAberta();

        $this->enviarComVariacoes($numero, function (string $destino) use ($url, $legenda) {
            $payload = [
                'number' => $destino,
                'mediatype' => 'image',
                'media' => $url,
            ];

            if ($legenda !== '') {
                $payload['caption'] = $legenda;
            }

            return $this->postar('/message/sendMedia/'.$this->instanciaCodificada(), $payload);
        });
    }

    /**
     * Consulta se o número possui WhatsApp e devolve o JID correto para envio.
     *
     * @return array{exists: bool, inconclusivo: bool, jid: ?string, numero_envio: ?string, numero_informado: string}
     */
    public function consultarNumero(string $numero): array
    {
        $informado = $this->formatarNumero($numero);

        if ($informado === '') {
            return $this->resultadoConsulta($numero, false, true, null, null);
        }

        if (! $this->configurado()) {
            return $this->resultadoConsulta($informado, false, true, null, null);
        }

        try {
            $candidatos = $this->candidatosNumero($informado);

            $resposta = Http::timeout($this->timeout)
                ->withHeaders(['apikey' => $this->apiKey])
                ->acceptJson()
                ->post(rtrim($this->baseUrl, '/').'/chat/whatsappNumbers/'.$this->instanciaCodificada(), [
                    'numbers' => $candidatos,
                ]);

            if ($resposta->failed()) {
                Log::warning('Evolution whatsappNumbers falhou.', [
                    'status' => $resposta->status(),
                    'body' => $resposta->body(),
                    'candidatos' => $candidatos,
                ]);

                return $this->resultadoConsulta($informado, false, true, null, null);
            }

            $lista = $this->normalizarListaConsulta($resposta->json());

            Log::info('Evolution whatsappNumbers resposta.', [
                'candidatos' => $candidatos,
                'lista' => $lista,
            ]);

            foreach ($lista as $item) {
                if (! is_array($item) || ! $this->itemPossuiWhatsApp($item)) {
                    continue;
                }

                $jid = isset($item['jid']) ? (string) $item['jid'] : null;
                $numeroEnvio = $jid !== null
                    ? $this->formatarNumero($jid)
                    : $this->formatarNumero((string) ($item['number'] ?? $informado));

                return $this->resultadoConsulta($informado, true, false, $jid, $numeroEnvio ?: null);
            }

            // Resposta válida da API, porém nenhum candidato com WhatsApp.
            return $this->resultadoConsulta($informado, false, false, null, null);
        } catch (\Throwable $e) {
            Log::warning('Evolution whatsappNumbers exceção.', ['erro' => $e->getMessage()]);

            return $this->resultadoConsulta($informado, false, true, null, null);
        }
    }

    /**
     * @param  callable(string): Response  $enviar
     */
    private function enviarComVariacoes(string $numero, callable $enviar): void
    {
        $informado = $this->formatarNumero($numero);
        $variacoes = $this->numerosParaTentativa($numero);
        $ultimoErro = null;

        foreach ($variacoes as $destino) {
            try {
                $resposta = $enviar($destino);
                $this->validarRespostaEnvio($resposta, $destino);

                if ($destino !== $informado) {
                    Log::info('Envio WhatsApp bem-sucedido com variação de número.', [
                        'informado' => $informado,
                        'utilizado' => $destino,
                    ]);
                }

                return;
            } catch (RuntimeException $e) {
                $ultimoErro = $e;
                Log::warning('Tentativa de envio WhatsApp falhou.', [
                    'destino' => $destino,
                    'erro' => $e->getMessage(),
                ]);
            }
        }

        throw $ultimoErro ?? new RuntimeException('Não foi possível enviar a mensagem pelo WhatsApp.');
    }

    /**
     * @return list<string>
     */
    private function numerosParaTentativa(string $numero): array
    {
        $informado = $this->formatarNumero($numero);
        $consulta = $this->consultarNumero($numero);
        $numeros = [];

        if ($consulta['numero_envio']) {
            $numeros[] = $consulta['numero_envio'];
        }

        foreach ($this->candidatosNumero($informado) as $candidato) {
            $numeros[] = $candidato;
        }

        return array_values(array_unique(array_filter($numeros)));
    }

    /**
     * @return array{exists: bool, inconclusivo: bool, jid: ?string, numero_envio: ?string, numero_informado: string}
     */
    private function resultadoConsulta(
        string $informado,
        bool $exists,
        bool $inconclusivo,
        ?string $jid,
        ?string $numeroEnvio,
    ): array {
        return [
            'exists' => $exists,
            'inconclusivo' => $inconclusivo,
            'jid' => $jid,
            'numero_envio' => $numeroEnvio,
            'numero_informado' => $informado,
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function normalizarListaConsulta(mixed $corpo): array
    {
        if (! is_array($corpo)) {
            return [];
        }

        if (isset($corpo[0]) && is_array($corpo[0])) {
            return $corpo;
        }

        foreach (['response', 'data', 'numbers', 'result'] as $chave) {
            if (isset($corpo[$chave]) && is_array($corpo[$chave])) {
                return array_is_list($corpo[$chave])
                    ? $corpo[$chave]
                    : array_values($corpo[$chave]);
            }
        }

        if (! array_is_list($corpo)) {
            $itens = [];

            foreach ($corpo as $chave => $valor) {
                if (! is_array($valor)) {
                    continue;
                }

                $valor['number'] = $valor['number'] ?? $this->formatarNumero((string) $chave);
                $itens[] = $valor;
            }

            return $itens;
        }

        return $corpo;
    }

    /**
     * @param  array<string, mixed>  $item
     */
    private function itemPossuiWhatsApp(array $item): bool
    {
        foreach (['exists', 'isWhatsApp', 'valid', 'whatsapp'] as $campo) {
            if (array_key_exists($campo, $item)) {
                return (bool) $item[$campo];
            }
        }

        return isset($item['jid']) && filled($item['jid']);
    }

    /**
     * @return list<string>
     */
    private function candidatosNumero(string $numero): array
    {
        $candidatos = [$numero];

        if (str_starts_with($numero, '55') && strlen($numero) === 13 && $numero[4] === '9') {
            $candidatos[] = substr($numero, 0, 4).substr($numero, 5);
        }

        if (str_starts_with($numero, '55') && strlen($numero) === 12) {
            $candidatos[] = substr($numero, 0, 4).'9'.substr($numero, 4);
        }

        return array_values(array_unique($candidatos));
    }

    private function instanciaCodificada(): string
    {
        return rawurlencode($this->instancia);
    }

    private function formatarNumero(string $numero): string
    {
        return preg_replace('/\D/', '', str_replace('@s.whatsapp.net', '', $numero)) ?? $numero;
    }

    private function garantirConexaoAberta(): void
    {
        $estado = $this->estadoConexao();

        if (! $estado['conectada']) {
            $state = $estado['state'] ?? 'desconhecido';

            throw new RuntimeException(
                "Instância WhatsApp não está conectada (estado: {$state}). Abra o painel Evolution, escaneie o QR Code novamente e tente de novo."
            );
        }
    }

    private function tentarEnvioTexto(string $numero, string $texto): Response
    {
        $caminho = '/message/sendText/'.$this->instanciaCodificada();

        $resposta = $this->postar($caminho, [
            'number' => $numero,
            'text' => $texto,
            'delay' => 1200,
            'linkPreview' => false,
        ]);

        if ($resposta->successful()) {
            return $resposta;
        }

        if ($resposta->status() === 400) {
            return $this->postar($caminho, [
                'number' => $numero,
                'textMessage' => ['text' => $texto],
                'delay' => 1200,
                'linkPreview' => false,
            ]);
        }

        return $resposta;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function postar(string $caminho, array $payload): Response
    {
        if (! $this->configurado()) {
            throw new RuntimeException('Evolution API não configurada.');
        }

        return Http::timeout($this->timeout)
            ->withHeaders([
                'apikey' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])
            ->acceptJson()
            ->post(rtrim($this->baseUrl, '/').$caminho, $payload);
    }

    private function validarRespostaEnvio(Response $resposta, string $numero): void
    {
        if ($resposta->failed()) {
            $corpo = $resposta->json();
            $mensagemApi = is_array($corpo)
                ? ($corpo['response']['message'] ?? $corpo['message'] ?? $corpo['error'] ?? null)
                : null;

            throw new RuntimeException(
                'Evolution API erro '.$resposta->status().': '.($mensagemApi ?? $resposta->body())
            );
        }

        $corpo = $resposta->json();

        Log::info('Evolution API resposta de envio.', [
            'numero' => $numero,
            'resposta' => $corpo,
        ]);

        if (is_array($corpo) && ($corpo['error'] ?? false) === true) {
            throw new RuntimeException(
                'Evolution API rejeitou o envio: '.($corpo['message'] ?? json_encode($corpo))
            );
        }
    }
}
