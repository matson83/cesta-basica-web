@extends('layouts.app')

@section('title', 'Criar Cesta')

@section('content')
    <div class="mb-8 pb-6 border-b border-[#e3e3e0]">
        <h1 class="text-xl font-semibold">Nova Cesta</h1>
        <p class="text-[#706f6c] text-sm">Monte a cesta, escolha a família e gere o pagamento via PIX</p>
    </div>

    <form action="{{ route('cestas-basicas.store') }}" method="POST" class="bg-white rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] p-6">
        @csrf
        <div class="grid grid-cols-1 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Nome da cesta</label>
                <input id="nomeCesta" name="nome" value="{{ old('nome') }}" required class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2" placeholder="Ex: Cesta Família Básica">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Descrição</label>
                <textarea id="descCesta" name="descricao" rows="2" class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2">{{ old('descricao') }}</textarea>
            </div>

            <div>
                <p class="text-sm font-medium mb-2">Produtos</p>
                <div class="space-y-2">
                    @forelse ($produtos as $p)
                        <label class="flex items-center gap-3">
                            <input type="checkbox" name="product[{{ $p->id }}][selected]" value="1" data-preco="{{ $p->preco }}" class="produto-check">
                            <span class="flex-1">{{ $p->nome }}</span>
                            <input name="product[{{ $p->id }}][qty]" type="number" min="0" value="0" class="w-20 text-sm border border-[#e3e3e0] rounded-sm px-2 py-1 produto-qtd">
                            <span class="w-24 text-right">R$ {{ number_format($p->preco, 2, ',', '.') }}</span>
                        </label>
                    @empty
                        <p class="text-sm text-[#706f6c]">Nenhum produto cadastrado. Cadastre produtos antes de montar uma cesta.</p>
                    @endforelse
                </div>
            </div>

            <div class="border-t border-[#e3e3e0] pt-4">
                <p class="text-sm font-medium mb-3">Família beneficiária</p>

                <div class="flex flex-wrap gap-4 mb-3 text-sm">
                    <label class="flex items-center gap-2">
                        <input type="radio" name="familia_modo" value="existente" class="familia-modo" {{ old('familia_modo', 'existente') === 'existente' ? 'checked' : '' }}>
                        Família existente
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="radio" name="familia_modo" value="nova" class="familia-modo" {{ old('familia_modo') === 'nova' ? 'checked' : '' }}>
                        Cadastrar nova família
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="radio" name="familia_modo" value="nenhuma" class="familia-modo" {{ old('familia_modo') === 'nenhuma' ? 'checked' : '' }}>
                        Só salvar a cesta (sem pagamento)
                    </label>
                </div>

                <div data-familia="existente" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium mb-1">Selecione a família</label>
                        <select name="familia_id" class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2">
                            <option value="">Selecione...</option>
                            @foreach ($familias as $familia)
                                <option value="{{ $familia->id }}" @selected(old('familia_id') == $familia->id)>{{ $familia->nome_responsavel }} — {{ $familia->bairro }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div data-familia="nova" class="hidden grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium mb-1">Nome do responsável</label>
                        <input name="familia[nome_responsavel]" value="{{ old('familia.nome_responsavel') }}" class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">CPF</label>
                        <input name="familia[cpf]" value="{{ old('familia.cpf') }}" placeholder="000.000.000-00" class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Nº de membros</label>
                        <input name="familia[num_membros]" type="number" min="1" value="{{ old('familia.num_membros', 1) }}" class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Telefone</label>
                        <input name="familia[telefone]" value="{{ old('familia.telefone') }}" placeholder="(00) 00000-0000" class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Bairro</label>
                        <input name="familia[bairro]" value="{{ old('familia.bairro') }}" class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2">
                    </div>
                    <div class="sm:col-span-3">
                        <label class="block text-sm font-medium mb-1">Endereço completo</label>
                        <textarea name="familia[endereco]" rows="2" class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2">{{ old('familia.endereco') }}</textarea>
                    </div>
                </div>

                <div data-familia-data class="mt-3 max-w-xs">
                    <label class="block text-sm font-medium mb-1">Data da entrega</label>
                    <input type="date" name="data_entrega" value="{{ old('data_entrega', now()->toDateString()) }}" class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2">
                </div>
            </div>

            <div class="flex justify-end items-center gap-4 border-t border-[#e3e3e0] pt-4">
                <div class="text-right">
                    <p class="text-sm text-[#706f6c]">Valor total</p>
                    <p id="totalCesta" class="text-xl font-semibold">R$ 0,00</p>
                </div>
                <button type="submit" id="btnSalvar" class="px-4 py-1.5 bg-[#1b1b18] text-white rounded-sm text-sm hover:bg-black transition-colors">Criar e gerar PIX</button>
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

            (function(){
                const existente = document.querySelector('[data-familia="existente"]');
                const nova = document.querySelector('[data-familia="nova"]');
                const dataEntrega = document.querySelector('[data-familia-data]');
                const btn = document.getElementById('btnSalvar');

                function aplicar(){
                    const modo = document.querySelector('.familia-modo:checked')?.value || 'existente';
                    existente.classList.toggle('hidden', modo !== 'existente');
                    nova.classList.toggle('hidden', modo !== 'nova');
                    nova.classList.toggle('grid', modo === 'nova');
                    dataEntrega.classList.toggle('hidden', modo === 'nenhuma');
                    btn.textContent = modo === 'nenhuma' ? 'Salvar cesta' : 'Criar e gerar PIX';
                }

                document.querySelectorAll('.familia-modo').forEach(el => el.addEventListener('change', aplicar));
                aplicar();
            })();
        </script>
    @endpush
@endsection
