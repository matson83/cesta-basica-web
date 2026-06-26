@extends('layouts.app')

@section('title', 'Criar Cesta')

@section('content')
    <div class="mb-8 pb-6 border-b border-[#e3e3e0]">
        <h1 class="text-xl font-semibold">Nova Cesta</h1>
        <p class="text-[#706f6c] text-sm">Monte a cesta, escolha a família e gere o pagamento via PIX</p>
    </div>

    <form action="{{ route('cestas-basicas.store') }}" method="POST" class="bg-white rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] p-4 sm:p-6">
        @csrf
        <div class="grid grid-cols-1 gap-4">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="sm:col-span-2">
                    <label for="nomeCesta" class="block text-sm font-medium mb-1">Nome da cesta</label>
                    <input id="nomeCesta" name="nome" value="{{ old('nome') }}" required class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]" placeholder="Ex: Cesta Família Básica">
                </div>
                <div>
                    <label for="categoriaCesta" class="block text-sm font-medium mb-1">Categoria</label>
                    <select id="categoriaCesta" name="categoria" class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
                        <option value="">Selecione...</option>
                        @foreach (['Padronizadas', 'Especiais'] as $cat)
                            <option value="{{ $cat }}" @selected(old('categoria') === $cat)>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label for="descCesta" class="block text-sm font-medium mb-1">Descrição</label>
                <textarea id="descCesta" name="descricao" rows="2" class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">{{ old('descricao') }}</textarea>
            </div>
            <label class="flex items-center gap-2 text-sm">
                <input type="hidden" name="ativo" value="0">
                <input type="checkbox" name="ativo" value="1" class="rounded-sm border-[#e3e3e0]" @checked(old('ativo', 1))>
                Cesta ativa
            </label>

            <div>
                <p class="text-sm font-medium mb-2">Produtos</p>
                @include('partials.cesta-produtos-picker', ['produtos' => $produtos])
            </div>

            <div class="border-t border-[#e3e3e0] pt-4">
                <p class="text-sm font-medium mb-2">Família beneficiária</p>

                <div class="flex flex-col sm:flex-row sm:flex-wrap gap-3 sm:gap-4 mb-3 text-sm">
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
                        <select name="familia_id" class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
                            <option value="">Selecione...</option>
                            @foreach ($familias as $familia)
                                <option value="{{ $familia->id }}" @selected(old('familia_id') == $familia->id)>{{ $familia->nome_responsavel }} - {{ $familia->bairro }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div data-familia="nova" class="hidden grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium mb-1">Nome do responsável</label>
                        <input name="familia[nome_responsavel]" value="{{ old('familia.nome_responsavel') }}" class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">CPF</label>
                        <input name="familia[cpf]" value="{{ old('familia.cpf') }}" placeholder="000.000.000-00" class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Nº de membros</label>
                        <input name="familia[num_membros]" type="number" min="1" value="{{ old('familia.num_membros', 1) }}" class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Telefone</label>
                        <input name="familia[telefone]" value="{{ old('familia.telefone') }}" placeholder="(00) 00000-0000" class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Bairro</label>
                        <input name="familia[bairro]" value="{{ old('familia.bairro') }}" class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
                    </div>
                    <div class="sm:col-span-3">
                        <label class="block text-sm font-medium mb-1">Endereço completo</label>
                        <textarea name="familia[endereco]" rows="2" class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">{{ old('familia.endereco') }}</textarea>
                    </div>
                </div>

                <div data-familia-data class="mt-3 max-w-xs">
                    <label class="block text-sm font-medium mb-1">Data da entrega</label>
                    <input type="date" name="data_entrega" value="{{ old('data_entrega', now()->toDateString()) }}" class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
                </div>
            </div>

            <div class="flex flex-col sm:flex-row sm:justify-end sm:items-center gap-4 border-t border-[#e3e3e0] pt-4">
                <div class="sm:text-right">
                    <p class="text-sm text-[#706f6c]">Valor total</p>
                    <p id="totalCesta" class="text-xl font-semibold">R$ 0,00</p>
                </div>
                <button type="submit" id="btnSalvar" class="px-4 py-2 sm:py-1.5 bg-[#1b1b18] text-white rounded-sm text-sm hover:bg-black transition-colors">Criar e gerar PIX</button>
            </div>
        </div>
    </form>

    @push('modals')
        <script>
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
