@extends('layouts.app')

@section('title', 'Editar Cesta')

@section('content')
    <div class="mb-8 pb-6 border-b border-[#e3e3e0]">
        <h1 class="text-xl font-semibold">Editar Cesta</h1>
        <p class="text-[#706f6c] text-sm">Atualize a composição e as informações da cesta</p>
    </div>

    <form action="{{ route('cestas-basicas.update', $cesta) }}" method="POST" class="bg-white rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] p-4 sm:p-6">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 gap-4">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="sm:col-span-2">
                    <label for="nomeCesta" class="block text-sm font-medium mb-1">Nome da cesta</label>
                    <input id="nomeCesta" name="nome" value="{{ old('nome', $cesta->nome) }}" required class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
                </div>
                <div>
                    <label for="categoriaCesta" class="block text-sm font-medium mb-1">Categoria</label>
                    <select id="categoriaCesta" name="categoria" class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
                        <option value="">Selecione...</option>
                        @foreach (['Padronizadas', 'Especiais'] as $cat)
                            <option value="{{ $cat }}" @selected(old('categoria', $cesta->categoria) === $cat)>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label for="descCesta" class="block text-sm font-medium mb-1">Descrição</label>
                <textarea id="descCesta" name="descricao" rows="2" class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">{{ old('descricao', $cesta->descricao) }}</textarea>
            </div>
            <label class="flex items-center gap-2 text-sm">
                <input type="hidden" name="ativo" value="0">
                <input type="checkbox" name="ativo" value="1" class="rounded-sm border-[#e3e3e0]" @checked(old('ativo', $cesta->ativo))>
                Cesta ativa
            </label>

            <div>
                <p class="text-sm font-medium mb-2">Produtos</p>
                <div class="space-y-2">
                    @forelse ($produtos as $p)
                        @php
                            $pivot = $cesta->produtos->firstWhere('id', $p->id)?->pivot;
                            $produtoQuantidade = old("product.{$p->id}.qty", $pivot->quantidade ?? 0);
                        @endphp
                        <div class="grid grid-cols-1 gap-2 rounded-sm border border-[#e3e3e0] p-3 sm:grid-cols-[minmax(0,1fr)_5rem_6rem] sm:items-center sm:border-0 sm:p-0" data-produto-row>
                            <input type="hidden" name="product[{{ $p->id }}][selected]" value="{{ (int) $produtoQuantidade > 0 ? 1 : 0 }}" class="produto-selected">
                            <label for="produtoQtdEditar{{ $p->id }}" class="min-w-0 text-sm">{{ $p->nome }}</label>
                            <input id="produtoQtdEditar{{ $p->id }}" name="product[{{ $p->id }}][qty]" type="number" min="0" value="{{ $produtoQuantidade }}" class="w-full text-sm border border-[#e3e3e0] rounded-sm px-2 py-1 produto-qtd" data-preco="{{ $p->preco }}">
                            <span class="text-left sm:text-right whitespace-nowrap">R$ {{ number_format($p->preco, 2, ',', '.') }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-[#706f6c]">Nenhum produto cadastrado.</p>
                    @endforelse
                </div>
            </div>

            <div class="flex flex-col sm:flex-row sm:justify-end sm:items-center gap-4 border-t border-[#e3e3e0] pt-4">
                <div class="sm:text-right">
                    <p class="text-sm text-[#706f6c]">Valor total</p>
                    <p id="totalCesta" class="text-xl font-semibold">R$ 0,00</p>
                </div>
                <button type="submit" class="px-4 py-2 sm:py-1.5 bg-[#1b1b18] text-white rounded-sm text-sm hover:bg-black transition-colors">Salvar alterações</button>
            </div>
        </div>
    </form>

    @push('modals')
        <script>
            (function(){
                function calc(){
                    let total = 0;
                    const quantidades = document.querySelectorAll('.produto-qtd');

                    quantidades.forEach((input)=>{
                        const qtd = Number(input.value || 0);
                        const selected = input.closest('[data-produto-row]')?.querySelector('.produto-selected');

                        if (selected) {
                            selected.value = qtd > 0 ? '1' : '0';
                        }

                        if(qtd > 0){
                            total += qtd * Number(input.dataset.preco || 0);
                        }
                    });

                    document.getElementById('totalCesta').textContent = 'R$ ' + total.toFixed(2).replace('.',',');
                }
                document.querySelectorAll('.produto-qtd').forEach(el=>el.addEventListener('input', calc));
                calc();
            })();
        </script>
    @endpush
@endsection
