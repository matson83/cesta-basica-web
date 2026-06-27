<?php

namespace App\Http\Controllers\Gestor;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\Gestor;
use App\Models\Impulsionamento;
use App\Services\WhatsApp\Contracts\WhatsAppGateway;
use App\Support\WhatsApp;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class ImpulsionamentoController extends Controller
{
    public function index(): View
    {
        $impulsionamentos = Impulsionamento::query()
            ->with('gestor')
            ->withCount([
                'empresas',
                'empresas as enviados_count' => fn ($query) => $query->whereNotNull('impulsionamento_empresa.enviado_em'),
            ])
            ->latest()
            ->paginate(10);

        return view('pages.gestor.impulsionamentos.index', compact('impulsionamentos'));
    }

    public function create(): View
    {
        return view('pages.gestor.impulsionamentos.create', [
            'impulsionamento' => null,
            'empresas' => $this->empresasDisponiveis(),
            'selecionadas' => [],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        $gestor = $this->gestorAtual();

        $impulsionamento = Impulsionamento::create([
            'gestor_id' => $gestor->id,
            'titulo' => $data['titulo'],
            'mensagem' => $data['mensagem'],
            'imagens' => $this->limparImagens($data['imagens'] ?? []),
        ]);

        $impulsionamento->empresas()->sync($data['empresas']);

        return redirect()
            ->route('gestor.impulsionamentos.show', $impulsionamento)
            ->with('status', 'Impulsionamento criado. Abra o WhatsApp de cada firma para enviar a mensagem.');
    }

    public function show(Impulsionamento $impulsionamento, WhatsAppGateway $gateway): View
    {
        $impulsionamento->load('gestor.user', 'empresas');

        $conexao = $gateway instanceof \App\Services\WhatsApp\EvolutionApiGateway
            ? $gateway->estadoConexao()
            : ['state' => null, 'conectada' => false, 'raw' => null];

        $whatsappPorEmpresa = [];

        if ($gateway instanceof \App\Services\WhatsApp\EvolutionApiGateway && ($conexao['conectada'] ?? false)) {
            foreach ($impulsionamento->empresas as $empresa) {
                $whatsappPorEmpresa[$empresa->id] = $gateway->consultarNumero((string) $empresa->telefone);
            }
        }

        return view('pages.gestor.impulsionamentos.show', [
            'impulsionamento' => $impulsionamento,
            'gatewayConfigurado' => $gateway->configurado(),
            'instanciaEvolution' => config('services.evolution.instance'),
            'conexaoEvolution' => $conexao,
            'whatsappPorEmpresa' => $whatsappPorEmpresa,
        ]);
    }

    public function edit(Impulsionamento $impulsionamento): View
    {
        return view('pages.gestor.impulsionamentos.edit', [
            'impulsionamento' => $impulsionamento,
            'empresas' => $this->empresasDisponiveis(),
            'selecionadas' => $impulsionamento->empresas()->pluck('empresas.id')->all(),
        ]);
    }

    public function update(Request $request, Impulsionamento $impulsionamento): RedirectResponse
    {
        $data = $this->validateData($request);

        $impulsionamento->update([
            'titulo' => $data['titulo'],
            'mensagem' => $data['mensagem'],
            'imagens' => $this->limparImagens($data['imagens'] ?? []),
        ]);

        $impulsionamento->empresas()->sync($data['empresas']);

        return redirect()
            ->route('gestor.impulsionamentos.show', $impulsionamento)
            ->with('status', 'Impulsionamento atualizado com sucesso.');
    }

    public function destroy(Impulsionamento $impulsionamento): RedirectResponse
    {
        $impulsionamento->delete();

        return redirect()
            ->route('gestor.impulsionamentos.index')
            ->with('status', 'Impulsionamento removido com sucesso.');
    }

    /**
     * Marca o envio para uma firma (o disparo em si é manual, via WhatsApp).
     */
    public function marcarEnviado(Impulsionamento $impulsionamento, Empresa $empresa): RedirectResponse
    {
        abort_unless($impulsionamento->empresas()->whereKey($empresa->id)->exists(), 404);

        $impulsionamento->empresas()->updateExistingPivot($empresa->id, [
            'enviado_em' => now(),
        ]);

        return back()->with('status', "Marcado como enviado para \"{$empresa->nome_fantasia}\".");
    }

    /**
     * Dispara automaticamente o impulsionamento (Evolution API) para todas as
     * firmas ainda pendentes.
     */
    public function disparar(Impulsionamento $impulsionamento, WhatsAppGateway $gateway): RedirectResponse
    {
        if (! $gateway->configurado()) {
            return back()->with('error', 'Gateway de WhatsApp não configurado. Defina EVOLUTION_API_URL, EVOLUTION_API_KEY e EVOLUTION_INSTANCE no .env.');
        }

        $pendentes = $impulsionamento->empresas()->wherePivotNull('enviado_em')->get();

        $enviados = 0;
        $falhas = 0;

        foreach ($pendentes as $empresa) {
            $resultado = $this->enviarPara($impulsionamento, $empresa, $gateway);
            $resultado['ok'] ? $enviados++ : $falhas++;
        }

        $mensagem = "Disparo concluído: {$enviados} enviada(s)".($falhas > 0 ? ", {$falhas} falha(s). Use o WhatsApp manual nas firmas pendentes." : '.');

        return back()->with($falhas > 0 ? 'error' : 'status', $mensagem);
    }

    /**
     * Envia automaticamente para uma única firma.
     */
    public function enviarFirma(Impulsionamento $impulsionamento, Empresa $empresa, WhatsAppGateway $gateway): RedirectResponse
    {
        abort_unless($impulsionamento->empresas()->whereKey($empresa->id)->exists(), 404);

        if (! $gateway->configurado()) {
            return back()->with('error', 'Gateway de WhatsApp não configurado. Defina EVOLUTION_API_URL, EVOLUTION_API_KEY e EVOLUTION_INSTANCE no .env.');
        }

        $resultado = $this->enviarPara($impulsionamento, $empresa, $gateway);

        if ($resultado['ok']) {
            $destino = WhatsApp::normalizar($empresa->telefone);

            return back()->with('status', "Mensagem enviada para \"{$empresa->nome_fantasia}\" (WhatsApp +{$destino}).");
        }

        return back()->with('error', "Falha ao enviar para \"{$empresa->nome_fantasia}\": {$resultado['erro']}");
    }

    /**
     * @return array{ok: bool, erro: ?string}
     */
    private function enviarPara(Impulsionamento $impulsionamento, Empresa $empresa, WhatsAppGateway $gateway): array
    {
        $numero = WhatsApp::normalizar($empresa->telefone);

        if ($numero === null) {
            return ['ok' => false, 'erro' => 'Telefone da firma inválido ou não cadastrado.'];
        }

        try {
            $imagens = $impulsionamento->imagensValidas();

            if ($imagens === []) {
                $gateway->enviarTexto($numero, $impulsionamento->mensagemBase());
            } else {
                $gateway->enviarImagem($numero, $imagens[0], $impulsionamento->mensagemBase());

                foreach (array_slice($imagens, 1) as $url) {
                    $gateway->enviarImagem($numero, $url);
                }
            }

            $impulsionamento->empresas()->updateExistingPivot($empresa->id, [
                'enviado_em' => now(),
            ]);

            return ['ok' => true, 'erro' => null];
        } catch (Throwable $e) {
            Log::warning('Falha ao enviar impulsionamento via WhatsApp.', [
                'impulsionamento_id' => $impulsionamento->id,
                'empresa_id' => $empresa->id,
                'telefone' => $numero,
                'erro' => $e->getMessage(),
            ]);

            return ['ok' => false, 'erro' => $e->getMessage()];
        }
    }

    /**
     * @return Collection<int, Empresa>
     */
    private function empresasDisponiveis(): Collection
    {
        return Empresa::query()
            ->orderBy('nome_fantasia')
            ->get(['id', 'nome_fantasia', 'telefone', 'ativo']);
    }

    /**
     * Garante (ou cria) o registro de gestor vinculado ao usuário autenticado.
     */
    private function gestorAtual(): Gestor
    {
        $user = auth()->user();

        return Gestor::firstOrCreate(
            ['user_id' => $user->id],
            ['nome' => $user->name],
        );
    }

    /**
     * @param  array<int, string|null>  $imagens
     * @return list<string>
     */
    private function limparImagens(array $imagens): array
    {
        return array_values(array_filter(
            array_map(static fn ($url) => trim((string) $url), $imagens),
            static fn (string $url) => $url !== '',
        ));
    }

    /**
     * @return array<string, mixed>
     */
    private function validateData(Request $request): array
    {
        return $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'mensagem' => ['required', 'string', 'max:4000'],
            'imagens' => ['nullable', 'array', 'max:10'],
            'imagens.*' => ['nullable', 'url', 'max:2048'],
            'empresas' => ['required', 'array', 'min:1'],
            'empresas.*' => ['integer', 'exists:empresas,id'],
        ]);
    }
}
