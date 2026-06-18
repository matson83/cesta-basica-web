@extends('layouts.app')

@section('title', 'Catálogo - Cestas Básicas')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8 pb-6 border-b border-[#e3e3e0]">
        <div>
            <h1 class="text-xl font-semibold mb-1">Catálogo de Cestas Básicas</h1>
            <p class="text-[#706f6c] text-sm">Listagem de cestas disponíveis para distribuição</p>
        </div>
        <a href="{{ route('cestas-basicas.create') }}" class="inline-flex items-center justify-center gap-1.5 px-4 py-2 sm:py-1.5 bg-[#1b1b18] text-white rounded-sm text-sm font-medium hover:bg-black transition-colors">Nova cesta</a>
    </div>

    <div class="bg-white rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] p-4 sm:p-5">
        <form method="GET" action="{{ route('cestas-basicas.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-5">
            <div class="md:col-span-2">
                <input id="searchCestas" type="search" name="busca" value="{{ request('busca') }}" placeholder="Buscar cesta..."
                       aria-label="Buscar cestas"
                       class="w-full pl-3 pr-3 py-2 text-sm border border-[#e3e3e0] rounded-sm focus:outline-none focus:border-[#1b1b18]">
            </div>
            <select name="categoria" class="text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
                <option value="">Todas as categorias</option>
                @foreach (['Padronizadas', 'Especiais'] as $cat)
                    <option value="{{ $cat }}" @selected(request('categoria') === $cat)>{{ $cat }}</option>
                @endforeach
            </select>
            <div class="flex flex-col sm:flex-row gap-2">
                <button type="submit" class="inline-flex justify-center px-4 py-2 text-sm bg-[#1b1b18] text-white rounded-sm hover:bg-black transition-colors">Filtrar</button>
                <a href="{{ route('cestas-basicas.index') }}" class="inline-flex justify-center px-4 py-2 text-sm border border-[#e3e3e0] rounded-sm hover:border-[#1b1b18] transition-colors">Limpar</a>
            </div>
        </form>

        <div class="app-table-wrap">
            <table class="app-table text-sm" style="--table-min-width: 54rem">
                <caption class="sr-only">Lista de cestas básicas</caption>
                <thead>
                    <tr class="border-b border-[#e3e3e0] text-left text-[#706f6c]">
                        <th scope="col" class="pb-3 font-medium">Nome</th>
                        <th scope="col" class="pb-3 font-medium">Categoria</th>
                        <th scope="col" class="pb-3 font-medium">Itens</th>
                        <th scope="col" class="pb-3 font-medium">Valor total</th>
                        <th scope="col" class="pb-3 font-medium">Status</th>
                        <th scope="col" class="pb-3 font-medium text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e3e3e0]">
                    @forelse ($cestas as $cesta)
                        <tr class="hover:bg-[#FDFDFC]">
                            <td class="py-3 font-medium">{{ $cesta->nome }}</td>
                            <td class="py-3 text-[#706f6c]">{{ $cesta->categoria ?? 'Sem categoria' }}</td>
                            <td class="py-3 text-[#706f6c]">{{ $cesta->total_itens }}</td>
                            <td class="py-3">R$ {{ number_format($cesta->valor_total, 2, ',', '.') }}</td>
                            <td class="py-3">
                                <span @class([
                                    'text-xs font-medium px-2 py-0.5 rounded-sm',
                                    'bg-emerald-50 text-emerald-700' => $cesta->ativo,
                                    'bg-[#dbdbd7] text-[#706f6c]' => ! $cesta->ativo,
                                ])>{{ $cesta->ativo ? 'Ativa' : 'Inativa' }}</span>
                            </td>
                            <td class="py-3 text-right">
                                <div class="app-table-actions">
                                    <a href="{{ route('cestas-basicas.show', $cesta) }}" class="px-2 py-1 text-xs border border-[#e3e3e0] rounded-sm hover:border-[#1b1b18] transition-colors">Visualizar</a>
                                    <a href="{{ route('cestas-basicas.edit', $cesta) }}" class="px-2 py-1 text-xs border border-[#e3e3e0] rounded-sm hover:border-[#1b1b18] transition-colors">Editar</a>
                                    <form action="{{ route('cestas-basicas.destroy', $cesta) }}" method="POST"
                                          onsubmit="return confirm('Remover esta cesta?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2 py-1 text-xs border border-[#e3e3e0] rounded-sm hover:border-[#1b1b18] transition-colors">Remover</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-[#706f6c]">Nenhuma cesta cadastrada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
