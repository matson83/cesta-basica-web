@extends('layouts.app')

@section('title', 'Dashboard — Cestas Básicas')

@section('content')
    <div class="mb-8 pb-6 border-b border-[#e3e3e0]">
        <h1 class="text-xl font-semibold">Dashboard — Cestas Básicas</h1>
        <p class="text-[#706f6c] text-sm">Visão geral das cestas e pedidos</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg p-4 text-center shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)]">
            <p class="text-[#706f6c] text-sm mb-1">Cestas cadastradas</p>
            <p class="text-2xl font-semibold text-[#1b1b18]">12</p>
        </div>
        <div class="bg-white rounded-lg p-4 text-center shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)]">
            <p class="text-[#706f6c] text-sm mb-1">Valor total estoque</p>
            <p class="text-2xl font-semibold text-emerald-600">R$ 8.450,00</p>
        </div>
        <div class="bg-white rounded-lg p-4 text-center shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)]">
            <p class="text-[#706f6c] text-sm mb-1">Pedidos pendentes</p>
            <p class="text-2xl font-semibold text-amber-600">3</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] p-5">
        <h2 class="text-sm font-semibold mb-3">Últimas cestas criadas</h2>
        <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <caption class="sr-only">Últimas cestas criadas</caption>
                <thead>
                    <tr class="border-b border-[#e3e3e0] text-left text-[#706f6c]">
                            <th scope="col" class="pb-3 font-medium">Nome</th>
                            <th scope="col" class="pb-3 font-medium">Itens</th>
                            <th scope="col" class="pb-3 font-medium">Valor</th>
                            <th scope="col" class="pb-3 font-medium text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e3e3e0]">
                    @foreach ([
                        ['nome' => 'Cesta Família Básica', 'itens' => 8, 'valor' => 'R$ 120,00'],
                        ['nome' => 'Cesta Jovem', 'itens' => 6, 'valor' => 'R$ 90,00'],
                        ['nome' => 'Cesta Proteção', 'itens' => 7, 'valor' => 'R$ 105,00'],
                    ] as $c)
                        <tr class="hover:bg-[#FDFDFC]">
                            <td class="py-3 font-medium">{{ $c['nome'] }}</td>
                            <td class="py-3 text-[#706f6c]">{{ $c['itens'] }}</td>
                            <td class="py-3">{{ $c['valor'] }}</td>
                            <td class="py-3 text-right">
                                    <button type="button" class="px-2 py-1 text-xs border border-[#e3e3e0] rounded-sm">Visualizar</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
