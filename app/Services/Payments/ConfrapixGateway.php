<?php

namespace App\Services\Payments;

use App\Services\Payments\Contracts\PaymentGateway;
use App\Services\Payments\Data\PixChargeResult;
use App\Services\Payments\Exceptions\PaymentGatewayException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Integração com o gateway de pagamentos Confrapix.
 *
 * @see https://doc.confrapix.com.br/
 */
class ConfrapixGateway implements PaymentGateway
{
    /**
     * @param  array<string, mixed>  $config
     */
    public function __construct(private readonly array $config)
    {
    }

    public function createPixCharge(array $payload): PixChargeResult
    {
        $body = array_filter([
            'amount' => round($payload['amount_in_cents'] / 100, 2),
            'customer_name' => Arr::get($payload, 'payer.name'),
            'customer_document' => preg_replace('/\D/', '', (string) Arr::get($payload, 'payer.cpf')),
            'description' => $payload['description'] ?? null,
            'expiration_date' => $payload['expiration_date'] ?? now()->addDay()->format('Y-m-d H:i:s'),
            'callback_url' => $payload['callback_url'] ?? null,
        ], fn ($value) => $value !== null && $value !== '');

        $response = $this->request()->post($this->endpoint('create_pix'), $body);

        return $this->toPixChargeResult($this->ensureSuccess($response));
    }

    /**
     * Consulta uma transação tentando uuid, id numérico e listagem.
     *
     * @param  array<int, string|null>  $alternateIds
     */
    public function getCharge(string $chargeId, array $alternateIds = []): PixChargeResult
    {
        $ids = array_values(array_unique(array_filter([$chargeId, ...$alternateIds])));

        foreach ($ids as $id) {
            foreach ($this->consultaPaths() as $path) {
                $url = str_replace('{id}', urlencode($id), $path);
                $response = $this->request()->get($url);

                if ($response->successful()) {
                    return $this->toPixChargeResult($response);
                }

                Log::debug('Confrapix consulta sem sucesso', [
                    'url' => $url,
                    'status' => $response->status(),
                ]);
            }
        }

        $fromList = $this->findInList($ids);

        if ($fromList !== null) {
            return $fromList;
        }

        throw new PaymentGatewayException(
            'Não foi possível consultar a transação no Confrapix. Verifique CONFRAPIX_ENDPOINT_GET_CHARGE e CONFRAPIX_ENDPOINT_LIST_CHARGES.'
        );
    }

    /**
     * @return array<int, string>
     */
    private function consultaPaths(): array
    {
        return array_values(array_unique(array_filter([
            $this->endpoint('get_charge'),
            $this->config['endpoints']['show_charge'] ?? '/api/transaction-ec/show/{id}',
        ])));
    }

    /**
     * Busca a transação na listagem do gateway (polling em lote).
     *
     * @param  array<int, string>  $ids
     */
    private function findInList(array $ids): ?PixChargeResult
    {
        $listPath = $this->config['endpoints']['list_charges'] ?? '/api/transaction-ec/index';

        if (blank($listPath)) {
            return null;
        }

        $response = $this->request()->get($listPath);

        if (! $response->successful()) {
            Log::debug('Confrapix listagem indisponível', [
                'path' => $listPath,
                'status' => $response->status(),
            ]);

            return null;
        }

        $json = $response->json();

        if (! is_array($json)) {
            return null;
        }

        $items = $this->extractTransactionList($json);

        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }

            $itemIds = array_filter([
                (string) ($item['uuid'] ?? ''),
                (string) ($item['id'] ?? ''),
            ]);

            if (array_intersect($ids, $itemIds)) {
                return $this->toPixChargeResultFromTransaction($item, $json);
            }
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $json
     * @return array<int, mixed>
     */
    private function extractTransactionList(array $json): array
    {
        foreach (['transactions', 'data', 'items', 'results'] as $key) {
            $value = $json[$key] ?? null;

            if (is_array($value) && array_is_list($value)) {
                return $value;
            }

            if (is_array($value) && isset($value['data']) && is_array($value['data'])) {
                return $value['data'];
            }
        }

        return [];
    }

    private function request(): PendingRequest
    {
        $token = $this->config['token'] ?? null;

        if (blank($token)) {
            throw new PaymentGatewayException('O token do Confrapix (CONFRAPIX_TOKEN) não está configurado.');
        }

        return Http::baseUrl(rtrim((string) $this->config['base_url'], '/'))
            ->withToken($token)
            ->acceptJson()
            ->asJson()
            ->timeout((int) ($this->config['timeout'] ?? 30));
    }

    private function endpoint(string $key): string
    {
        $endpoint = Arr::get($this->config, "endpoints.{$key}");

        if (blank($endpoint)) {
            throw new PaymentGatewayException("Endpoint do Confrapix \"{$key}\" não está configurado.");
        }

        return $endpoint;
    }

    private function ensureSuccess(Response $response): Response
    {
        if ($response->failed()) {
            $corpo = $response->json() ?? $response->body();

            Log::warning('Confrapix retornou erro', [
                'status' => $response->status(),
                'body' => $corpo,
            ]);

            $detalhe = is_array($corpo)
                ? ($corpo['message'] ?? json_encode($corpo, JSON_UNESCAPED_UNICODE))
                : (string) $corpo;

            throw new PaymentGatewayException(
                'Falha ao comunicar com o gateway Confrapix (HTTP '.$response->status().'). '.$detalhe
            );
        }

        return $response;
    }

    private function toPixChargeResult(Response $response): PixChargeResult
    {
        $json = $response->json();

        Log::debug('Confrapix resposta', ['body' => $json ?? $response->body()]);

        if (! is_array($json)) {
            throw new PaymentGatewayException(
                'Resposta inesperada do gateway Confrapix: '.$response->body()
            );
        }

        $tx = $json['transaction'] ?? $json['data'] ?? $json;

        if (! is_array($tx)) {
            throw new PaymentGatewayException(
                'Resposta inesperada do gateway Confrapix: '.json_encode($json, JSON_UNESCAPED_UNICODE)
            );
        }

        return $this->toPixChargeResultFromTransaction($tx, $json);
    }

    /**
     * @param  array<string, mixed>  $tx
     * @param  array<string, mixed>  $json
     */
    private function toPixChargeResultFromTransaction(array $tx, array $json): PixChargeResult
    {
        $pix = is_array($tx['pix'] ?? null) ? $tx['pix'] : [];
        $data = array_merge($tx, $pix);

        try {
            return new PixChargeResult(
                id: (string) $this->pick($data, ['uuid', 'id', 'transaction_id']),
                status: $this->resolveStatus($tx),
                qrCode: $this->pick($data, ['code', 'qr_code', 'pix_code', 'copy_paste', 'copia_cola', 'emv', 'brcode']) ?: null,
                qrCodeImage: $this->pick($data, ['qr_code_image', 'qr_code_base64', 'qrcode_base64', 'qr_code_url', 'image', 'url']) ?: null,
                amountInCents: ($valor = $this->pick($tx, ['amount', 'value', 'valor'])) !== null ? (int) round(((float) $valor) * 100) : null,
                expiresAt: $this->pick($tx, ['expired_in', 'expiration_date', 'expires_at', 'expiration', 'due_date']) ?: null,
                raw: $json,
            );
        } catch (Throwable $e) {
            throw new PaymentGatewayException(
                'Resposta inesperada do gateway Confrapix: '.json_encode($json, JSON_UNESCAPED_UNICODE),
                previous: $e
            );
        }
    }

    /**
     * O Confrapix marca pagamento com confirmed=true e/ou payed_in preenchido.
     *
     * @param  array<string, mixed>  $tx
     */
    private function resolveStatus(array $tx): string
    {
        if (! empty($tx['confirmed']) || ! empty($tx['payed_in'])) {
            return 'paid';
        }

        return (string) ($tx['status'] ?? 'pending');
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, string>  $keys
     */
    private function pick(array $data, array $keys): mixed
    {
        foreach ($keys as $key) {
            if (isset($data[$key]) && $data[$key] !== '') {
                return $data[$key];
            }
        }

        return null;
    }
}
