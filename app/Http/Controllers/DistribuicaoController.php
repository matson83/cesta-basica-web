<?php

namespace App\Http\Controllers;

use App\Models\Cesta;
use App\Models\Distribuicao;
use App\Models\Familia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DistribuicaoController extends Controller
{
    public function index(Request $request): View
    {
        $empresaId = $this->empresaIdAtual();

        $distribuicoes = Distribuicao::query()
            ->where('empresa_id', $empresaId)
            ->with(['familia', 'cesta.produtos', 'pagamento'])
            ->when($request->string('status')->toString(), function ($query, string $status) {
                $query->whereIn('status', self::statusValuesForFilter($status));
            })
            ->when($request->date('data'), function ($query, $data) {
                $query->whereDate('data_entrega', $data);
            })
            ->latest('data_entrega')
            ->get();

        $base = fn () => Distribuicao::where('empresa_id', $empresaId);

        $stats = [
            'mes' => $base()->whereMonth('data_entrega', now()->month)
                ->whereYear('data_entrega', now()->year)
                ->count(),
            'pendentes' => $base()->whereIn('status', self::statusValuesForFilter(Distribuicao::STATUS_PENDENTE))->count(),
            'pagas' => $base()->whereIn('status', self::statusValuesForFilter(Distribuicao::STATUS_PAGO))->count(),
        ];

        $familias = Familia::where('empresa_id', $empresaId)->where('ativo', true)->orderBy('nome_responsavel')->get();
        $cestas = Cesta::where('empresa_id', $empresaId)->where('ativo', true)->orderBy('nome')->get();

        return view('pages.distribuicoes.index', compact('distribuicoes', 'stats', 'familias', 'cestas'));
    }

    public function store(Request $request): RedirectResponse
    {
        Distribuicao::create($this->validateData($request) + [
            'empresa_id' => $this->empresaIdAtual(),
        ]);

        return redirect()
            ->route('distribuicoes.index')
            ->with('status', 'Distribuição registrada com sucesso.');
    }

    public function update(Request $request, Distribuicao $distribuicao): RedirectResponse
    {
        $this->autorizarEmpresa($distribuicao);

        if (! $distribuicao->update($this->validateData($request))) {
            return redirect()
                ->route('distribuicoes.index')
                ->with('error', 'Não foi possível atualizar a distribuição.');
        }

        return redirect()
            ->route('distribuicoes.index')
            ->with('status', 'Distribuição atualizada com sucesso.');
    }

    public function destroy(Distribuicao $distribuicao): RedirectResponse
    {
        $this->autorizarEmpresa($distribuicao);

        $distribuicao->delete();

        return redirect()
            ->route('distribuicoes.index')
            ->with('status', 'Distribuição removida com sucesso.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateData(Request $request): array
    {
        $request->merge([
            'cesta_id' => $request->filled('cesta_id') ? $request->input('cesta_id') : null,
        ]);

        $empresaId = $this->empresaIdAtual();

        $data = $request->validate([
            'familia_id' => ['required', Rule::exists('familias', 'id')->where('empresa_id', $empresaId)],
            'cesta_id' => ['nullable', Rule::exists('cestas', 'id')->where('empresa_id', $empresaId)],
            'data_entrega' => ['required', 'date'],
            'responsavel' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:'.implode(',', Distribuicao::statusValidos())],
            'observacoes' => ['nullable', 'string'],
        ]);

        $data['status'] = Distribuicao::normalizeStatus($data['status']);

        return $data;
    }

    /**
     * @return list<string>
     */
    private static function statusValuesForFilter(string $status): array
    {
        return match (Distribuicao::normalizeStatus($status)) {
            Distribuicao::STATUS_PAGO => [Distribuicao::STATUS_PAGO, 'paga', 'entregue'],
            Distribuicao::STATUS_CANCELADO => [Distribuicao::STATUS_CANCELADO, 'cancelada'],
            default => [Distribuicao::STATUS_PENDENTE],
        };
    }
}
