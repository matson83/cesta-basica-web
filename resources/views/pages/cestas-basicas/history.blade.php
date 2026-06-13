@extends('layouts.app')

@section('title', 'Histórico de Pedidos')

@section('content')
    <div class="mb-8 pb-6 border-b border-[#e3e3e0]">
        <h1 class="text-xl font-semibold">Histórico de Pedidos</h1>
        <p class="text-[#706f6c] text-sm">Pedidos realizados (dados fictícios)</p>
    </div>

    <div class="bg-white rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] p-5">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[#e3e3e0] text-left text-[#706f6c]">
                        <th scope="col" class="pb-3 font-medium">Pedido</th>
                        <th scope="col" class="pb-3 font-medium">Data</th>
                        <th scope="col" class="pb-3 font-medium">Cesta</th>
                        <th scope="col" class="pb-3 font-medium">Valor</th>
                        <th scope="col" class="pb-3 font-medium">Status</th>
                        <th scope="col" class="pb-3 font-medium text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e3e3e0]">
                    @foreach ([
                        ['id' => 5021, 'data' => '10/06/2026', 'cesta' => 'Cesta Família Básica', 'valor' => 'R$ 120,00', 'status' => 'Entregue'],
                        ['id' => 5020, 'data' => '09/06/2026', 'cesta' => 'Cesta Proteção', 'valor' => 'R$ 95,50', 'status' => 'Pendente'],
                        ['id' => 5019, 'data' => '08/06/2026', 'cesta' => 'Cesta Emergencial', 'valor' => 'R$ 60,00', 'status' => 'Cancelado'],
                    ] as $o)
                        <tr class="hover:bg-[#FDFDFC]">
                            <td class="py-3 text-[#706f6c]">#{{ $o['id'] }}</td>
                            <td class="py-3">{{ $o['data'] }}</td>
                            <td class="py-3 font-medium">{{ $o['cesta'] }}</td>
                            <td class="py-3">{{ $o['valor'] }}</td>
                            <td class="py-3">
                                <span @class([
                                    'text-xs font-medium px-2 py-0.5 rounded-sm',
                                    'bg-emerald-50 text-emerald-700' => $o['status'] === 'Entregue',
                                    'bg-amber-50 text-amber-700' => $o['status'] === 'Pendente',
                                    'bg-red-50 text-[#f53003]' => $o['status'] === 'Cancelado',
                                ])>{{ $o['status'] }}</span>
                            </td>
                            <td class="py-3 text-right">
                                <button class="px-2 py-1 text-xs border border-[#e3e3e0] rounded-sm">Visualizar</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
