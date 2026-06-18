@extends('layouts.app')

@section('title', 'Carrinho — Cestas Básicas')

@section('content')
    <div class="mb-8 pb-6 border-b border-[#e3e3e0]">
        <h1 class="text-xl font-semibold">Carrinho</h1>
        <p class="text-[#706f6c] text-sm">Itens selecionados para compra/registro</p>
    </div>

    @php
        $cart = [
            ['nome' => 'Cesta Família Básica', 'qtd' => 1, 'valor' => 120.00],
            ['nome' => 'Cesta Proteção', 'qtd' => 1, 'valor' => 95.50],
        ];
        $subtotal = array_sum(array_map(fn($i) => $i['qtd'] * $i['valor'], $cart));
    @endphp

    <div class="bg-white rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] p-4 sm:p-5">
        <div class="app-table-wrap">
            <table class="app-table text-sm" style="--table-min-width: 40rem">
                <caption class="sr-only">Itens no carrinho</caption>
                <thead>
                        <tr class="border-b border-[#e3e3e0] text-left text-[#706f6c]">
                            <th scope="col" class="pb-3 font-medium">Cesta</th>
                            <th scope="col" class="pb-3 font-medium">Quantidade</th>
                            <th scope="col" class="pb-3 font-medium text-right">Valor unit.</th>
                            <th scope="col" class="pb-3 font-medium text-right">Subtotal</th>
                        </tr>
                </thead>
                <tbody class="divide-y divide-[#e3e3e0]">
                    @foreach ($cart as $item)
                        <tr>
                            <td class="py-3 font-medium">{{ $item['nome'] }}</td>
                            <td class="py-3">{{ $item['qtd'] }}</td>
                            <td class="py-3 text-right">R$ {{ number_format($item['valor'],2,',','.') }}</td>
                            <td class="py-3 text-right">R$ {{ number_format($item['qtd'] * $item['valor'],2,',','.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mt-4 pt-4 border-t border-[#e3e3e0]">
            <div class="text-sm text-[#706f6c]">Total de itens: {{ array_sum(array_column($cart, 'qtd')) }}</div>
            <div class="sm:text-right">
                <p class="text-sm text-[#706f6c]">Subtotal</p>
                <p class="text-xl font-semibold">R$ {{ number_format($subtotal,2,',','.') }}</p>
                <button type="button" class="w-full sm:w-auto inline-block mt-3 px-4 py-2 sm:py-1.5 bg-[#1b1b18] text-white rounded-sm text-sm">Finalizar compra</button>
            </div>
        </div>
    </div>
@endsection
