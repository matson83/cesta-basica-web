<?php

namespace App\Http\Controllers;

use App\Models\Familia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FamiliaController extends Controller
{
    public function index(Request $request): View
    {
        $familias = Familia::query()
            ->where('empresa_id', $this->empresaIdAtual())
            ->when($request->string('busca')->toString(), function ($query, string $busca) {
                $query->where('nome_responsavel', 'like', "%{$busca}%")
                    ->orWhere('cpf', 'like', "%{$busca}%");
            })
            ->when($request->filled('ativo'), function ($query) use ($request) {
                $query->where('ativo', $request->boolean('ativo'));
            })
            ->orderBy('nome_responsavel')
            ->paginate(10)
            ->withQueryString();

        return view('pages.familias.index', compact('familias'));
    }

    public function store(Request $request): RedirectResponse
    {
        $familia = Familia::create($this->validateData($request) + [
            'empresa_id' => $this->empresaIdAtual(),
        ]);

        return redirect()
            ->route('familias.index')
            ->with('status', "Família de \"{$familia->nome_responsavel}\" cadastrada com sucesso.");
    }

    public function update(Request $request, Familia $familia): RedirectResponse
    {
        $this->autorizarEmpresa($familia);

        $familia->update($this->validateData($request, $familia));

        return redirect()
            ->route('familias.index')
            ->with('status', 'Família atualizada com sucesso.');
    }

    public function destroy(Familia $familia): RedirectResponse
    {
        $this->autorizarEmpresa($familia);

        $familia->delete();

        return redirect()
            ->route('familias.index')
            ->with('status', 'Família removida com sucesso.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateData(Request $request, ?Familia $familia = null): array
    {
        return $request->validate([
            'nome_responsavel' => ['required', 'string', 'max:255'],
            'cpf' => [
                'required', 'string', 'max:14',
                Rule::unique('familias', 'cpf')
                    ->where('empresa_id', $this->empresaIdAtual())
                    ->ignore($familia?->id),
            ],
            'num_membros' => ['required', 'integer', 'min:1'],
            'telefone' => ['nullable', 'string', 'max:20'],
            'bairro' => ['nullable', 'string', 'max:255'],
            'endereco' => ['nullable', 'string'],
            'ativo' => ['boolean'],
        ]);
    }
}
