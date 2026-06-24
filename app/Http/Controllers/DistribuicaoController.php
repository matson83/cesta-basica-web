<?php

namespace App\Http\Controllers;

use App\Models\Cesta;
use App\Models\Distribuicao;
use App\Models\Familia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DistribuicaoController extends Controller
{
    public function index(Request $request): View
    {
        $distribuicoes = Distribuicao::query()
            ->with(['familia', 'cesta.produtos', 'pagamento'])
            ->when($request->string('status')->toString(), function ($query, string $status) {
                $query->whereIn('status', self::statusValuesForFilter($status));
            })
            ->when($request->date('data'), function ($query, $data) {
                $query->whereDate('data_entrega', $data);
            })
            ->latest('data_entrega')
            ->get();

        $stats = [
            'mes' => Distribuicao::whereMonth('data_entrega', now()->month)
                ->whereYear('data_entrega', now()->year)
                ->count(),
            'pendentes' => Distribuicao::whereIn('status', self::statusValuesForFilter(Distribuicao::STATUS_PENDENTE))->count(),
            'pagas' => Distribuicao::whereIn('status', self::statusValuesForFilter(Distribuicao::STATUS_PAGO))->count(),
        ];

        $familias = Familia::where('ativo', true)->orderBy('nome_responsavel')->get();
        $cestas = Cesta::where('ativo', true)->orderBy('nome')->get();

        return view('pages.distribuicoes.index', compact('distribuicoes', 'stats', 'familias', 'cestas'));
    }

    public function store(Request $request): RedirectResponse
    {
        Distribuicao::create($this->validateData($request));

        return redirect()
            ->route('distribuicoes.index')
            ->with('status', 'Distribuição registrada com sucesso.');
    }

    public function update(Request $request, Distribuicao $distribuicao): RedirectResponse
    {
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

        $data = $request->validate([
            'familia_id' => ['required', 'exists:familias,id'],
            'cesta_id' => ['nullable', 'exists:cestas,id'],
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
