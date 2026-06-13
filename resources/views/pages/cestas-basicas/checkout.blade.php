@extends('layouts.app')

@section('title', 'Checkout — Cestas Básicas')

@section('content')
    <div class="mb-8 pb-6 border-b border-[#e3e3e0]">
        <h1 class="text-xl font-semibold">Checkout</h1>
        <p class="text-[#706f6c] text-sm">Confirme dados e escolha forma de pagamento</p>
    </div>

    @php
        $order = ['itens' => 2, 'valor' => 215.50];
    @endphp

    <div class="bg-white rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h2 class="text-sm font-semibold mb-3">Resumo do pedido</h2>
                <p class="text-sm text-[#706f6c] mb-2">Itens: {{ $order['itens'] }}</p>
                <p class="text-sm text-[#706f6c] mb-2">Valor total: <strong>R$ {{ number_format($order['valor'],2,',','.') }}</strong></p>
                <div class="mt-4">
                    <p class="text-sm font-medium mb-2">Forma de pagamento</p>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2"><input type="radio" name="pag" checked> PIX</label>
                        <label class="flex items-center gap-2"><input type="radio" name="pag"> Boleto</label>
                        <label class="flex items-center gap-2"><input type="radio" name="pag"> Cartão (não implementado)</label>
                    </div>
                </div>
            </div>
            <div>
                <h2 class="text-sm font-semibold mb-3">Dados do pagador</h2>
                <div class="space-y-3">
                    <input id="payerName" name="payer[name]" placeholder="Nome completo" aria-label="Nome completo" class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2">
                    <input id="payerCpf" name="payer[cpf]" placeholder="CPF" aria-label="CPF" class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2">
                    <input id="payerPhone" name="payer[phone]" placeholder="Telefone" aria-label="Telefone" class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2">
                </div>
                <div class="flex justify-end mt-4">
                    <button type="button" class="px-4 py-1.5 bg-[#1b1b18] text-white rounded-sm text-sm">Gerar PIX</button>
                </div>
            </div>
        </div>
    </div>
@endsection
