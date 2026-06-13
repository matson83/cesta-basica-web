@extends('layouts.app')

@section('title', 'Produtos')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8 pb-6 border-b border-[#e3e3e0]">
        <div>
            <h1 class="text-xl font-semibold mb-1">Produtos</h1>
            <p class="text-[#706f6c] text-sm">Itens que compõem a cesta básica</p>
        </div>
        <button type="button" data-dialog-open="modalProduto"
                class="inline-flex items-center gap-1.5 px-4 py-1.5 bg-[#1b1b18] text-white rounded-sm text-sm font-medium hover:bg-black transition-colors">
            Novo produto
        </button>
    </div>

    <div class="bg-white rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] p-5">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-5">
            <div class="md:col-span-1 relative">
                <input type="search" placeholder="Buscar produto..."
                       class="w-full pl-3 pr-3 py-2 text-sm border border-[#e3e3e0] rounded-sm focus:outline-none focus:border-[#1b1b18]">
            </div>
            <select class="text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
                <option selected>Todas as categorias</option>
                <option>Grãos e cereais</option>
                <option>Proteínas</option>
                <option>Higiene</option>
                <option>Bebidas</option>
            </select>
            <select class="text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
                <option selected>Todos os estoques</option>
                <option>Estoque baixo</option>
                <option>Em falta</option>
            </select>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[#e3e3e0] text-left text-[#706f6c]">
                        <th class="pb-3 font-medium">Produto</th>
                        <th class="pb-3 font-medium">Categoria</th>
                        <th class="pb-3 font-medium">Unidade</th>
                        <th class="pb-3 font-medium">Estoque</th>
                        <th class="pb-3 font-medium">Qtd. por cesta</th>
                        <th class="pb-3 font-medium text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e3e3e0]">
                    @forelse ($produtos as $produto)
                        <tr class="hover:bg-[#FDFDFC]">
                            <td class="py-3 font-medium">{{ $produto->nome }}</td>
                            <td class="py-3 text-[#706f6c]">{{ $produto->categoria }}</td>
                            <td class="py-3 text-[#706f6c]">{{ $produto->unidade }}</td>
                            <td class="py-3">
                                <span @class([
                                    'text-xs font-medium px-2 py-0.5 rounded-sm',
                                    'bg-emerald-50 text-emerald-700' => $produto->estoque > 20,
                                    'bg-amber-50 text-amber-700' => $produto->estoque > 10 && $produto->estoque <= 20,
                                    'bg-red-50 text-[#f53003]' => $produto->estoque <= 10,
                                ])>{{ $produto->estoque }}</span>
                            </td>
                            <td class="py-3">{{ $produto->quantidade_por_cesta }}</td>
                            <td class="py-3 text-right">
                                <form action="{{ route('produtos.destroy', $produto) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Remover este produto?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2 py-1 text-xs border border-[#e3e3e0] rounded-sm hover:border-[#1b1b18] transition-colors">Remover</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-[#706f6c]">Nenhum produto cadastrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('modals')
    <dialog id="modalProduto" data-form-dialog @if ($errors->any() && old('nome')) data-reopen="true" @endif
            class="backdrop:bg-black/40 bg-transparent p-0 max-w-md w-full rounded-lg open:animate-in">
        <div class="bg-white rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] p-6">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-base font-semibold">Novo produto</h2>
                <button type="button" data-dialog-close class="text-[#706f6c] hover:text-[#1b1b18] text-xl leading-none">&times;</button>
            </div>
            <form action="{{ route('produtos.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="nomeProduto" class="block text-sm font-medium mb-1">Nome do produto</label>
                    <input type="text" id="nomeProduto" name="nome" value="{{ old('nome') }}" required placeholder="Ex: Arroz branco 5kg"
                           class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="categoriaProduto" class="block text-sm font-medium mb-1">Categoria</label>
                        <select id="categoriaProduto" name="categoria" class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
                            <option value="">Selecione...</option>
                            @foreach (['Grãos e cereais', 'Proteínas', 'Alimentos', 'Higiene', 'Bebidas'] as $cat)
                                <option value="{{ $cat }}" @selected(old('categoria') === $cat)>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="unidadeProduto" class="block text-sm font-medium mb-1">Unidade</label>
                        <select id="unidadeProduto" name="unidade" required class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
                            <option value="">Selecione...</option>
                            @foreach (['unidade', 'pacote', 'lata', 'caixa'] as $un)
                                <option value="{{ $un }}" @selected(old('unidade') === $un)>{{ $un }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="estoqueProduto" class="block text-sm font-medium mb-1">Estoque inicial</label>
                        <input type="number" id="estoqueProduto" name="estoque" min="0" value="{{ old('estoque', 0) }}" required
                               class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
                    </div>
                    <div>
                        <label for="qtdCesta" class="block text-sm font-medium mb-1">Qtd. por cesta</label>
                        <input type="number" id="qtdCesta" name="quantidade_por_cesta" min="1" value="{{ old('quantidade_por_cesta', 1) }}" required
                               class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
                    </div>
                    <div>
                        <label for="precoProduto" class="block text-sm font-medium mb-1">Preço (R$)</label>
                        <input type="number" id="precoProduto" name="preco" min="0" step="0.01" value="{{ old('preco', 0) }}"
                               class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" data-dialog-close class="px-4 py-1.5 text-sm border border-[#e3e3e0] rounded-sm hover:border-[#1b1b18] transition-colors">Cancelar</button>
                    <button type="submit" class="px-4 py-1.5 text-sm bg-[#1b1b18] text-white rounded-sm hover:bg-black transition-colors">Salvar</button>
                </div>
            </form>
        </div>
    </dialog>
@endpush
