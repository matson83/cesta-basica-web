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
                @include('partials.cesta-produtos-picker', ['produtos' => $produtos, 'cesta' => $cesta])
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
@endsection
