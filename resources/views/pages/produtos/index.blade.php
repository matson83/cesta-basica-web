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
                    @foreach ([
                        ['nome' => 'Arroz branco 5kg', 'cat' => 'Grãos e cereais', 'un' => 'pacote', 'estoque' => 85, 'qtd' => 1, 'ok' => true],
                        ['nome' => 'Feijão carioca 1kg', 'cat' => 'Grãos e cereais', 'un' => 'pacote', 'estoque' => 120, 'qtd' => 2, 'ok' => true],
                        ['nome' => 'Óleo de soja 900ml', 'cat' => 'Alimentos', 'un' => 'unidade', 'estoque' => 12, 'qtd' => 1, 'ok' => false],
                        ['nome' => 'Açúcar cristal 1kg', 'cat' => 'Alimentos', 'un' => 'pacote', 'estoque' => 64, 'qtd' => 1, 'ok' => true],
                        ['nome' => 'Macarrão espaguete 500g', 'cat' => 'Grãos e cereais', 'un' => 'pacote', 'estoque' => 98, 'qtd' => 2, 'ok' => true],
                        ['nome' => 'Café torrado 500g', 'cat' => 'Bebidas', 'un' => 'pacote', 'estoque' => 8, 'qtd' => 1, 'ok' => false],
                        ['nome' => 'Sabão em barra', 'cat' => 'Higiene', 'un' => 'unidade', 'estoque' => 200, 'qtd' => 4, 'ok' => true],
                        ['nome' => 'Leite em pó 400g', 'cat' => 'Alimentos', 'un' => 'lata', 'estoque' => 18, 'qtd' => 1, 'ok' => 'warn'],
                    ] as $produto)
                        <tr class="hover:bg-[#FDFDFC]">
                            <td class="py-3 font-medium">{{ $produto['nome'] }}</td>
                            <td class="py-3 text-[#706f6c]">{{ $produto['cat'] }}</td>
                            <td class="py-3 text-[#706f6c]">{{ $produto['un'] }}</td>
                            <td class="py-3">
                                <span @class([
                                    'text-xs font-medium px-2 py-0.5 rounded-sm',
                                    'bg-emerald-50 text-emerald-700' => $produto['ok'] === true,
                                    'bg-red-50 text-[#f53003]' => $produto['ok'] === false,
                                    'bg-amber-50 text-amber-700' => $produto['ok'] === 'warn',
                                ])>{{ $produto['estoque'] }}</span>
                            </td>
                            <td class="py-3">{{ $produto['qtd'] }}</td>
                            <td class="py-3 text-right">
                                <button class="px-2 py-1 text-xs border border-[#e3e3e0] rounded-sm hover:border-[#1b1b18] transition-colors">Editar</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('modals')
    <dialog id="modalProduto" data-form-dialog
            class="backdrop:bg-black/40 bg-transparent p-0 max-w-md w-full rounded-lg open:animate-in">
        <div class="bg-white rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] p-6">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-base font-semibold">Novo produto</h2>
                <button type="button" data-dialog-close class="text-[#706f6c] hover:text-[#1b1b18] text-xl leading-none">&times;</button>
            </div>
            <form class="space-y-4">
                <div>
                    <label for="nomeProduto" class="block text-sm font-medium mb-1">Nome do produto</label>
                    <input type="text" id="nomeProduto" required placeholder="Ex: Arroz branco 5kg"
                           class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="categoriaProduto" class="block text-sm font-medium mb-1">Categoria</label>
                        <select id="categoriaProduto" required class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
                            <option value="">Selecione...</option>
                            <option>Grãos e cereais</option>
                            <option>Proteínas</option>
                            <option>Alimentos</option>
                            <option>Higiene</option>
                            <option>Bebidas</option>
                        </select>
                    </div>
                    <div>
                        <label for="unidadeProduto" class="block text-sm font-medium mb-1">Unidade</label>
                        <select id="unidadeProduto" required class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
                            <option value="">Selecione...</option>
                            <option>unidade</option>
                            <option>pacote</option>
                            <option>lata</option>
                            <option>caixa</option>
                        </select>
                    </div>
                    <div>
                        <label for="estoqueProduto" class="block text-sm font-medium mb-1">Estoque inicial</label>
                        <input type="number" id="estoqueProduto" min="0" value="0" required
                               class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
                    </div>
                    <div>
                        <label for="qtdCesta" class="block text-sm font-medium mb-1">Qtd. por cesta</label>
                        <input type="number" id="qtdCesta" min="1" value="1" required
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
