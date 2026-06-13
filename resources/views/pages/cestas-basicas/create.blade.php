@extends('layouts.app')

@section('title', 'Criar Cesta')

@section('content')
    <div class="mb-8 pb-6 border-b border-[#e3e3e0]">
        <h1 class="text-xl font-semibold">Criar Cesta</h1>
        <p class="text-[#706f6c] text-sm">Formulário para criar uma nova cesta (mock)</p>
    </div>

    @php
        $produtos = [
            ['id'=>1,'nome'=>'Arroz 5kg','preco'=>25.00],
            ['id'=>2,'nome'=>'Feijão 1kg','preco'=>8.50],
            ['id'=>3,'nome'=>'Óleo 900ml','preco'=>6.00],
            ['id'=>4,'nome'=>'Açúcar 1kg','preco'=>4.50],
        ];
    @endphp

    <form class="bg-white rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] p-6" onsubmit="event.preventDefault(); alert('Mock: formulário não envia.');">
        <div class="grid grid-cols-1 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Nome da cesta</label>
                <input id="nomeCesta" class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2" placeholder="Ex: Cesta Família Básica">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Descrição</label>
                <textarea id="descCesta" rows="2" class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2"></textarea>
            </div>

            <div>
                <p class="text-sm font-medium mb-2">Produtos</p>
                <div class="space-y-2">
                    @foreach ($produtos as $p)
                        <label class="flex items-center gap-3">
                            <input type="checkbox" name="product[{{ $p['id'] }}][selected]" data-preco="{{ $p['preco'] }}" class="produto-check">
                            <span class="flex-1">{{ $p['nome'] }}</span>
                            <input name="product[{{ $p['id'] }}][qty]" type="number" min="0" value="0" class="w-20 text-sm border border-[#e3e3e0] rounded-sm px-2 py-1 produto-qtd">
                            <span class="w-24 text-right">R$ {{ number_format($p['preco'],2,',','.') }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="flex justify-end items-center gap-4">
                <div class="text-right">
                    <p class="text-sm text-[#706f6c]">Valor total</p>
                    <p id="totalCesta" class="text-xl font-semibold">R$ 0,00</p>
                </div>
                <button type="submit" class="px-4 py-1.5 bg-[#1b1b18] text-white rounded-sm text-sm">Salvar</button>
            </div>
        </div>
    </form>

    @push('modals')
        <script>
            (function(){
                function calc(){
                    let total = 0;
                    document.querySelectorAll('.produto-check').forEach((cb, i)=>{
                        const qtd = Number(document.querySelectorAll('.produto-qtd')[i].value || 0);
                        if(cb.checked && qtd>0){
                            total += qtd * Number(cb.dataset.preco);
                        }
                    });
                    document.getElementById('totalCesta').textContent = 'R$ ' + total.toFixed(2).replace('.',',');
                }
                document.querySelectorAll('.produto-check, .produto-qtd').forEach(el=>el.addEventListener('input', calc));
            })();
        </script>
    @endpush
@endsection
