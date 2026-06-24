<?php

namespace App\Http\Controllers;

use App\Models\Distribuicao;
use App\Models\Pagamento;
use App\Services\Payments\Exceptions\PaymentGatewayException;
use App\Services\Payments\PagamentoService;
use App\Services\Payments\PagamentoSync;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PagamentoController extends Controller
{
    public function __construct(
        private readonly PagamentoService $pagamentos,
        private readonly PagamentoSync $sync,
    ) {
    }

    /**
     * Gera (ou reaproveita) a cobrança PIX de uma distribuição e leva o
     * usuário para a tela do PIX.
     */
    public function pagar(Distribuicao $distribuicao): RedirectResponse
    {
        $distribuicao->loadMissing('pagamento');

        if ($pagamento = $distribuicao->pagamento) {
            if ($pagamento->status === Pagamento::STATUS_PAGO) {
                return redirect()
                    ->route('pagamentos.comprovante', $pagamento)
                    ->with('status', 'Esta distribuição já foi paga.');
            }

            if ($pagamento->status === Pagamento::STATUS_PENDENTE && filled($pagamento->pix_copia_cola)) {
                return redirect()->route('pagamentos.pix', $pagamento);
            }
        }

        try {
            $pagamento = $this->pagamentos->criarPixParaDistribuicao($distribuicao);
        } catch (PaymentGatewayException $e) {
            return redirect()
                ->route('distribuicoes.index')
                ->with('error', 'Não foi possível gerar o PIX: '.$e->getMessage());
        }

        return redirect()->route('pagamentos.pix', $pagamento);
    }

    public function pix(Pagamento $pagamento): View|RedirectResponse
    {
        $pagamento->loadMissing('distribuicao.familia');

        if ($pagamento->status === Pagamento::STATUS_PENDENTE) {
            $this->sync->sync($pagamento->refresh());
        }

        if ($pagamento->status === Pagamento::STATUS_PAGO) {
            return redirect()->route('pagamentos.comprovante', $pagamento);
        }

        return view('pages.cestas-basicas.pix', compact('pagamento'));
    }

    public function comprovante(Pagamento $pagamento): View|RedirectResponse
    {
        if (! $pagamento->isPago()) {
            return redirect()
                ->route('pagamentos.pix', $pagamento)
                ->with('error', 'O comprovante só está disponível para pagamentos confirmados.');
        }

        $pagamento->loadMissing([
            'distribuicao.familia',
            'distribuicao.cesta.produtos',
        ]);

        return view('pages.pagamentos.comprovante', compact('pagamento'));
    }

    /**
     * Endpoint de polling: consulta o gateway, atualiza o pagamento e
     * devolve o status atual em JSON para a página de PIX.
     */
    public function status(Pagamento $pagamento): JsonResponse
    {
        if ($pagamento->status === Pagamento::STATUS_PENDENTE) {
            $this->sync->sync($pagamento->refresh());
        }

        return response()->json([
            'status' => $pagamento->status,
            'pago' => $pagamento->status === Pagamento::STATUS_PAGO,
            'finalizado' => $pagamento->status !== Pagamento::STATUS_PENDENTE,
            'redirect' => $pagamento->status === Pagamento::STATUS_PAGO
                ? route('pagamentos.comprovante', $pagamento)
                : null,
        ]);
    }
}
