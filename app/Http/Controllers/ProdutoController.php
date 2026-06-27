<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProdutoController extends Controller
{
    public function index(Request $request): View
    {
        $produtos = Produto::query()
            ->where('empresa_id', $this->empresaIdAtual())
            ->when($request->string('busca')->toString(), function ($query, string $busca) {
                $query->where('nome', 'like', "%{$busca}%");
            })
            ->when($request->string('categoria')->toString(), function ($query, string $categoria) {
                $query->where('categoria', $categoria);
            })
            ->orderBy('nome')
            ->get();

        return view('pages.produtos.index', compact('produtos'));
    }

    public function store(Request $request): RedirectResponse
    {
        $produto = Produto::create($this->validateData($request) + [
            'empresa_id' => $this->empresaIdAtual(),
        ]);

        return redirect()
            ->route('produtos.index')
            ->with('status', "Produto \"{$produto->nome}\" cadastrado com sucesso.");
    }

    public function update(Request $request, Produto $produto): RedirectResponse
    {
        $this->autorizarEmpresa($produto);

        $produto->update($this->validateData($request));

        return redirect()
            ->route('produtos.index')
            ->with('status', "Produto \"{$produto->nome}\" atualizado com sucesso.");
    }

    public function destroy(Produto $produto): RedirectResponse
    {
        $this->autorizarEmpresa($produto);

        $produto->delete();

        return redirect()
            ->route('produtos.index')
            ->with('status', 'Produto removido com sucesso.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateData(Request $request): array
    {
        return $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'categoria' => ['nullable', 'string', 'max:255'],
            'unidade' => ['required', 'string', 'max:50'],
            'estoque' => ['required', 'integer', 'min:0'],
            'quantidade_por_cesta' => ['required', 'integer', 'min:1'],
            'preco' => ['nullable', 'numeric', 'min:0'],
            'ativo' => ['boolean'],
        ]);
    }
}
