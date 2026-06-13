<?php

namespace App\Http\Controllers;

use App\Models\Cesta;
use App\Models\Distribuicao;
use App\Models\Familia;
use App\Models\Produto;
use App\Services\Payments\Exceptions\PaymentGatewayException;
use App\Services\Payments\PagamentoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CestaBasicaController extends Controller
{
    public function __construct(private readonly PagamentoService $pagamentos)
    {
    }

    public function index(Request $request): View
    {
        $cestas = Cesta::query()
            ->withCount('produtos')
            ->with('produtos')
            ->when($request->string('busca')->toString(), function ($query, string $busca) {
                $query->where('nome', 'like', "%{$busca}%");
            })
            ->when($request->string('categoria')->toString(), function ($query, string $categoria) {
                $query->where('categoria', $categoria);
            })
            ->orderBy('nome')
            ->get();

        return view('pages.cestas-basicas.index', compact('cestas'));
    }

    public function create(): View
    {
        $produtos = Produto::orderBy('nome')->get();
        $familias = Familia::where('ativo', true)->orderBy('nome_responsavel')->get();

        return view('pages.cestas-basicas.create', compact('produtos', 'familias'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        $modo = $request->input('familia_modo', 'existente');

        if ($modo !== 'nenhuma') {
            $request->validate([
                'data_entrega' => ['required', 'date'],
                'familia_id' => [Rule::requiredIf($modo === 'existente'), 'nullable', 'exists:familias,id'],
                'familia.nome_responsavel' => [Rule::requiredIf($modo === 'nova'), 'nullable', 'string', 'max:255'],
                'familia.cpf' => [Rule::requiredIf($modo === 'nova'), 'nullable', 'string', 'max:14', 'unique:familias,cpf'],
                'familia.num_membros' => ['nullable', 'integer', 'min:1'],
                'familia.telefone' => ['nullable', 'string', 'max:20'],
                'familia.bairro' => ['nullable', 'string', 'max:255'],
                'familia.endereco' => ['nullable', 'string'],
            ]);
        }

        $cesta = Cesta::create([
            'nome' => $data['nome'],
            'descricao' => $data['descricao'] ?? null,
            'categoria' => $data['categoria'] ?? null,
            'ativo' => $data['ativo'] ?? true,
        ]);

        $cesta->produtos()->sync($this->mapProdutos($data['product'] ?? []));

        if ($modo === 'nenhuma') {
            return redirect()
                ->route('cestas-basicas.show', $cesta)
                ->with('status', "Cesta \"{$cesta->nome}\" criada com sucesso.");
        }

        $familia = $modo === 'nova'
            ? Familia::create([
                'nome_responsavel' => $request->input('familia.nome_responsavel'),
                'cpf' => $request->input('familia.cpf'),
                'num_membros' => $request->integer('familia.num_membros') ?: 1,
                'telefone' => $request->input('familia.telefone'),
                'bairro' => $request->input('familia.bairro'),
                'endereco' => $request->input('familia.endereco'),
                'ativo' => true,
            ])
            : Familia::findOrFail($request->integer('familia_id'));

        $distribuicao = Distribuicao::create([
            'familia_id' => $familia->id,
            'cesta_id' => $cesta->id,
            'data_entrega' => $request->date('data_entrega'),
            'responsavel' => 'Administrador',
            'status' => Distribuicao::STATUS_PENDENTE,
        ]);

        try {
            $pagamento = $this->pagamentos->criarPixParaDistribuicao($distribuicao);
        } catch (PaymentGatewayException $e) {
            return redirect()
                ->route('distribuicoes.index')
                ->with('error', 'Cesta e distribuição criadas, mas não foi possível gerar o PIX: '.$e->getMessage());
        }

        return redirect()->route('pagamentos.pix', $pagamento);
    }

    public function show(Cesta $cestas_basica): View
    {
        $cesta = $cestas_basica->load('produtos');

        return view('pages.cestas-basicas.show', compact('cesta'));
    }

    public function edit(Cesta $cestas_basica): View
    {
        $cesta = $cestas_basica->load('produtos');
        $produtos = Produto::orderBy('nome')->get();

        return view('pages.cestas-basicas.edit', compact('cesta', 'produtos'));
    }

    public function update(Request $request, Cesta $cestas_basica): RedirectResponse
    {
        $data = $this->validateData($request);

        $cestas_basica->update([
            'nome' => $data['nome'],
            'descricao' => $data['descricao'] ?? null,
            'categoria' => $data['categoria'] ?? null,
            'ativo' => $data['ativo'] ?? true,
        ]);

        $cestas_basica->produtos()->sync($this->mapProdutos($data['product'] ?? []));

        return redirect()
            ->route('cestas-basicas.show', $cestas_basica)
            ->with('status', 'Cesta atualizada com sucesso.');
    }

    public function destroy(Cesta $cestas_basica): RedirectResponse
    {
        $cestas_basica->delete();

        return redirect()
            ->route('cestas-basicas.index')
            ->with('status', 'Cesta removida com sucesso.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateData(Request $request): array
    {
        return $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'descricao' => ['nullable', 'string'],
            'categoria' => ['nullable', 'string', 'max:255'],
            'ativo' => ['boolean'],
            'product' => ['array'],
            'product.*.selected' => ['nullable'],
            'product.*.qty' => ['nullable', 'integer', 'min:0'],
        ]);
    }

    /**
     * Constrói o payload de sincronização do pivô a partir do input do formulário.
     *
     * @param  array<int, array{selected?: mixed, qty?: mixed}>  $produtos
     * @return array<int, array{quantidade: int}>
     */
    private function mapProdutos(array $produtos): array
    {
        $sync = [];

        foreach ($produtos as $produtoId => $dados) {
            $quantidade = (int) ($dados['qty'] ?? 0);
            $selecionado = ! empty($dados['selected']) || $quantidade > 0;

            if ($selecionado && $quantidade > 0) {
                $sync[$produtoId] = ['quantidade' => $quantidade];
            }
        }

        return $sync;
    }
}
