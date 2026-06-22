<?php

namespace App\Services\Payments;

use App\Models\Distribuicao;
use App\Models\Pagamento;
use App\Services\Payments\Contracts\PaymentGateway;
use App\Services\Payments\Exceptions\PaymentGatewayException;
use Illuminate\Support\Facades\Log;

class PagamentoSync
{
    public function __construct(private readonly PaymentGateway $gateway)
    {
    }

    /**
     * @return bool true se conseguiu consultar o gateway.
     */
    public function sync(Pagamento $pagamento): bool
    {
        if (blank($pagamento->charge_id)) {
            return false;
        }

        $alternateId = (string) ($pagamento->payload_gateway['transaction']['id'] ?? '');

        try {
            $cobranca = $this->gateway instanceof ConfrapixGateway
                ? $this->gateway->getCharge($pagamento->charge_id, array_filter([$alternateId]))
                : $this->gateway->getCharge($pagamento->charge_id);
        } catch (PaymentGatewayException $e) {
            Log::warning('Falha ao sincronizar pagamento com Confrapix', [
                'pagamento_id' => $pagamento->id,
                'charge_id' => $pagamento->charge_id,
                'erro' => $e->getMessage(),
            ]);

            return false;
        }

        $pagamento->update([
            'status' => $this->mapStatus($cobranca->status, $cobranca->raw),
            'pix_copia_cola' => $cobranca->qrCode ?: $pagamento->pix_copia_cola,
            'pix_qr_code_base64' => $cobranca->qrCodeImage ?: $pagamento->pix_qr_code_base64,
            'expira_em' => $cobranca->expiresAt ?: $pagamento->expira_em,
            'payload_gateway' => $cobranca->raw,
        ]);

        $this->refletirNaDistribuicao($pagamento->refresh());

        return true;
    }

    public function refletirNaDistribuicao(Pagamento $pagamento): void
    {
        if ($pagamento->status !== Pagamento::STATUS_PAGO || blank($pagamento->distribuicao_id)) {
            return;
        }

        Distribuicao::where('id', $pagamento->distribuicao_id)
            ->where('status', Distribuicao::STATUS_PENDENTE)
            ->update(['status' => Distribuicao::STATUS_PAGO]);
    }

    /**
     * @param  array<string, mixed>  $raw
     */
    public function mapStatus(string $status, array $raw = []): string
    {
        $tx = $raw['transaction'] ?? $raw;

        if (is_array($tx) && (! empty($tx['confirmed']) || ! empty($tx['payed_in']))) {
            return Pagamento::STATUS_PAGO;
        }

        return match (strtolower($status)) {
            'paid', 'completed', 'approved', 'confirmed', 'pago', 'payed' => Pagamento::STATUS_PAGO,
            'expired', 'expirado' => Pagamento::STATUS_EXPIRADO,
            'canceled', 'cancelled', 'cancelado', 'failed', 'refused' => Pagamento::STATUS_CANCELADO,
            'processing', 'pending', 'pendente', 'waiting' => Pagamento::STATUS_PENDENTE,
            default => Pagamento::STATUS_PENDENTE,
        };
    }
}
