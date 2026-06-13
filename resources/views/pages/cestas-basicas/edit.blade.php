@extends('layouts.app')

@section('title', 'Editar Cesta')

@section('content')
    <div class="mb-8 pb-6 border-b border-[#e3e3e0]">
        <h1 class="text-xl font-semibold">Editar Cesta</h1>
        <p class="text-[#706f6c] text-sm">Edição visual (mock) da cesta</p>
    </div>

    <form action="{{ route('cestas-basicas.update', $cesta) }}" method="POST" class="bg-white rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] p-6">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Nome da cesta</label>
                <input id="nomeCesta" name="nome" value="{{ old('nome', $cesta->nome) }}" required class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Descrição</label>
                <textarea id="descCesta" name="descricao" rows="2" class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2">{{ old('descricao', $cesta->descricao) }}</textarea>
            </div>

            <div>
                <p class="text-sm font-medium mb-2">Produtos</p>
                <div class="space-y-2">
                    @forelse ($produtos as $p)
                        @php $pivot = $cesta->produtos->firstWhere('id', $p->id)?->pivot; @endphp
                        <label class="flex items-center gap-3">
                            <input type="checkbox" name="product[{{ $p->id }}][selected]" value="1" data-preco="{{ $p->preco }}" class="produto-check" @checked($pivot)>
                            <span class="flex-1">{{ $p->nome }}</span>
                            <input name="product[{{ $p->id }}][qty]" type="number" min="0" value="{{ $pivot->quantidade ?? 0 }}" class="w-20 text-sm border border-[#e3e3e0] rounded-sm px-2 py-1 produto-qtd" data-preco="{{ $p->preco }}">
                            <span class="w-24 text-right">R$ {{ number_format($p->preco, 2, ',', '.') }}</span>
                        </label>
                    @empty
                        <p class="text-sm text-[#706f6c]">Nenhum produto cadastrado.</p>
                    @endforelse
                </div>
            </div>

            <div class="flex justify-end items-center gap-4">
                <div class="text-right">
                    <p class="text-sm text-[#706f6c]">Valor total</p>
                    <p id="totalCesta" class="text-xl font-semibold">R$ 0,00</p>
                </div>
                <button type="submit" class="px-4 py-1.5 bg-[#1b1b18] text-white rounded-sm text-sm">Salvar alterações</button>
            </div>
        </div>
    </form>

    @push('modals')
        <script>
            (function(){
                function calc(){
                    let total = 0;
                    document.querySelectorAll('.produto-qtd').forEach((el)=>{
                        const qtd = Number(el.value || 0);
                        const preco = Number(el.dataset.preco || 0);
                        total += qtd * preco;
                    });
                    document.getElementById('totalCesta').textContent = 'R$ ' + total.toFixed(2).replace('.',',');
                }
                document.querySelectorAll('.produto-qtd').forEach(el=>el.addEventListener('input', calc));
                calc();
            })();
        </script>
    @endpush
@endsection
