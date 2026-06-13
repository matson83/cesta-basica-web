@extends('layouts.app')

@section('title', 'Catálogo — Cestas Básicas')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8 pb-6 border-b border-[#e3e3e0]">
        <div>
            <h1 class="text-xl font-semibold mb-1">Catálogo de Cestas Básicas</h1>
            <p class="text-[#706f6c] text-sm">Listagem de cestas disponíveis para distribuição</p>
        </div>
        <div>
            <button type="button" class="inline-flex items-center gap-1.5 px-4 py-1.5 bg-[#1b1b18] text-white rounded-sm text-sm font-medium hover:bg-black transition-colors">Nova cesta</button>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] p-5">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-5">
            <div class="md:col-span-1 relative">
                <input id="searchCestas" type="search" placeholder="Buscar cesta..."
                       aria-label="Buscar cestas"
                       class="w-full pl-3 pr-3 py-2 text-sm border border-[#e3e3e0] rounded-sm focus:outline-none focus:border-[#1b1b18]">
            </div>
            <select class="text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
                <option selected>Todas as categorias</option>
                <option>Padronizadas</option>
                <option>Especiais</option>
            </select>
            <select class="text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
                <option selected>Todos os status</option>
                <option>Ativa</option>
                <option>Inativa</option>
            </select>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <caption class="sr-only">Lista de cestas básicas</caption>
                <thead>
                    <tr class="border-b border-[#e3e3e0] text-left text-[#706f6c]">
                        <th scope="col" class="pb-3 font-medium">Nome</th>
                        <th scope="col" class="pb-3 font-medium">Itens</th>
                        <th scope="col" class="pb-3 font-medium">Valor total</th>
                        <th scope="col" class="pb-3 font-medium">Status</th>
                        <th scope="col" class="pb-3 font-medium text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e3e3e0]">
                    @foreach ([
                        ['nome' => 'Cesta Família Básica', 'itens' => 8, 'valor' => 'R$ 120,00', 'status' => 'Ativa'],
                        ['nome' => 'Cesta Proteção', 'itens' => 6, 'valor' => 'R$ 95,50', 'status' => 'Ativa'],
                        ['nome' => 'Cesta Emergencial', 'itens' => 5, 'valor' => 'R$ 60,00', 'status' => 'Inativa'],
                    ] as $cesta)
                        <tr class="hover:bg-[#FDFDFC]">
                            <td class="py-3 font-medium">{{ $cesta['nome'] }}</td>
                            <td class="py-3 text-[#706f6c]">{{ $cesta['itens'] }}</td>
                            <td class="py-3">{{ $cesta['valor'] }}</td>
                            <td class="py-3">
                                <span @class([
                                    'text-xs font-medium px-2 py-0.5 rounded-sm',
                                    'bg-emerald-50 text-emerald-700' => $cesta['status'] === 'Ativa',
                                    'bg-[#dbdbd7] text-[#706f6c]' => $cesta['status'] !== 'Ativa',
                                ])>{{ $cesta['status'] }}</span>
                            </td>
                            <td class="py-3 text-right">
                                <button type="button" class="px-2 py-1 text-xs border border-[#e3e3e0] rounded-sm hover:border-[#1b1b18] transition-colors">Visualizar</button>
                                <button type="button" class="px-2 py-1 text-xs border border-[#e3e3e0] rounded-sm hover:border-[#1b1b18] transition-colors">Editar</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
