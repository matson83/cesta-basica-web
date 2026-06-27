<?php

namespace App\Services\Payments;

use App\Models\Distribuicao;
use App\Models\Empresa;
use App\Models\Pagamento;
use App\Services\Payments\Exceptions\PaymentGatewayException;
use Illuminate\Support\Str;

class PagamentoService
{
    public function __construct(
        private readonly PaymentGatewayFactory $gateways,
        private readonly PagamentoSync $sync,
    ) {
    }

    /**
     * Cria uma cobrança PIX para uma distribuição, usando o valor total da
     * cesta e os dados da família como pagador. O token Confrapix utilizado é
     * o da firma (EC) dona da distribuição.
     */
    public function criarPixParaDistribuicao(Distribuicao $distribuicao): Pagamento
    {
        $distribuicao->loadMissing(['cesta.produtos', 'familia', 'empresa']);

        $cesta = $distribuicao->cesta;
        $familia = $distribuicao->familia;
        $valorCentavos = (int) round((float) ($cesta?->valor_total ?? 0) * 100);

        if ($valorCentavos < 1) {
            throw new PaymentGatewayException('A cesta não possui valor para cobrança (adicione produtos com preço).');
        }

        return $this->criarPix([
            'empresa_id' => $distribuicao->empresa_id,
            'distribuicao_id' => $distribuicao->id,
            'valor_centavos' => $valorCentavos,
            'pagador_nome' => $familia?->nome_responsavel,
            'pagador_cpf' => $familia?->cpf,
            'pagador_telefone' => $familia?->telefone,
            'descricao' => trim('Cesta '.($cesta?->nome ?? '').' - '.($familia?->nome_responsavel ?? '')),
        ], $distribuicao->empresa);
    }

    /**
     * @param  array<string, mixed>  $dados
     */
    public function criarPix(array $dados, ?Empresa $empresa = null): Pagamento
    {
        $empresa ??= filled($dados['empresa_id'] ?? null)
            ? Empresa::find($dados['empresa_id'])
            : null;

        $gateway = $this->gateways->forEmpresa($empresa);

        $pagamento = Pagamento::create([
            'empresa_id' => $empresa?->id ?? ($dados['empresa_id'] ?? null),
            'distribuicao_id' => $dados['distribuicao_id'] ?? null,
            'referencia' => (string) Str::uuid(),
            'metodo' => 'pix',
            'status' => Pagamento::STATUS_PENDENTE,
            'valor_centavos' => $dados['valor_centavos'],
            'pagador_nome' => $dados['pagador_nome'] ?? null,
            'pagador_cpf' => $dados['pagador_cpf'] ?? null,
            'pagador_email' => $dados['pagador_email'] ?? null,
            'pagador_telefone' => $dados['pagador_telefone'] ?? null,
        ]);

        try {
            $cobranca = $gateway->createPixCharge([
                'amount_in_cents' => $pagamento->valor_centavos,
                'description' => $dados['descricao'] ?? ('Pedido '.$pagamento->referencia),
                'reference' => $pagamento->referencia,
                'payer' => [
                    'name' => $pagamento->pagador_nome,
                    'cpf' => $pagamento->pagador_cpf,
                    'email' => $pagamento->pagador_email,
                    'phone' => $pagamento->pagador_telefone,
                ],
            ]);
        } catch (PaymentGatewayException $e) {
            $pagamento->update(['status' => Pagamento::STATUS_CANCELADO]);

            throw $e;
        }

        $pagamento->update([
            'charge_id' => $cobranca->id ?: null,
            'status' => $this->sync->mapStatus($cobranca->status, $cobranca->raw),
            'pix_copia_cola' => $cobranca->qrCode,
            'pix_qr_code_base64' => $cobranca->qrCodeImage,
            'expira_em' => $cobranca->expiresAt,
            'payload_gateway' => $cobranca->raw,
        ]);

        $this->sync->refletirNaDistribuicao($pagamento);

        return $pagamento;
    }
}
