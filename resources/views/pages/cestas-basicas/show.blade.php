@extends('layouts.app')

@section('title', 'Detalhes da Cesta')

@section('content')
    <div class="mb-8 pb-6 border-b border-[#e3e3e0]">
        <h1 class="text-xl font-semibold">Detalhes da Cesta</h1>
        <p class="text-[#706f6c] text-sm">Visualização completa da composição da cesta</p>
    </div>

    @php
        $cesta = [
            'nome' => 'Cesta Família Básica',
            'descricao' => 'Composição padrão para famílias de 4 pessoas.',
            'produtos' => [
                ['nome' => 'Arroz 5kg', 'qtd' => 1, 'valor' => 25.00],
                ['nome' => 'Feijão 1kg', 'qtd' => 2, 'valor' => 8.50],
                ['nome' => 'Óleo 900ml', 'qtd' => 1, 'valor' => 6.00],
                ['nome' => 'Açúcar 1kg', 'qtd' => 1, 'valor' => 4.50],
                ['nome' => 'Macarrão 500g', 'qtd' => 2, 'valor' => 3.75],
            ],
        ];
        $total = array_sum(array_map(fn($p) => $p['qtd'] * $p['valor'], $cesta['produtos']));
    @endphp

    <div class="bg-white rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] p-6">
        <div class="flex items-start justify-between gap-6 mb-6">
            <div>
                <h2 class="text-lg font-semibold">{{ $cesta['nome'] }}</h2>
                <p class="text-sm text-[#706f6c] mt-1">{{ $cesta['descricao'] }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-[#706f6c]">Itens</p>
                <p class="text-xl font-semibold">{{ array_sum(array_column($cesta['produtos'], 'qtd')) }}</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm mb-4">
                <caption class="sr-only">Produtos incluídos na cesta</caption>
                <thead>
                    <tr class="border-b border-[#e3e3e0] text-left text-[#706f6c]">
                        <th scope="col" class="pb-3 font-medium">Produto</th>
                        <th scope="col" class="pb-3 font-medium">Quantidade</th>
                        <th scope="col" class="pb-3 font-medium text-right">Valor unit.</th>
                        <th scope="col" class="pb-3 font-medium text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e3e3e0]">
                    @foreach ($cesta['produtos'] as $p)
                        <tr class="hover:bg-[#FDFDFC]">
                            <td class="py-3">{{ $p['nome'] }}</td>
                            <td class="py-3">{{ $p['qtd'] }}</td>
                            <td class="py-3 text-right">R$ {{ number_format($p['valor'], 2, ',', '.') }}</td>
                            <td class="py-3 text-right">R$ {{ number_format($p['qtd'] * $p['valor'], 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="flex justify-end items-center gap-4 pt-4 border-t border-[#e3e3e0]">
            <div class="text-right">
                <p class="text-sm text-[#706f6c]">Valor total</p>
                <p class="text-xl font-semibold">R$ {{ number_format($total, 2, ',', '.') }}</p>
            </div>
        </div>
    </div>
@endsection
