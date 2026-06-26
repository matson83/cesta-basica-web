@extends('layouts.app')

@section('title', 'Pagamento confirmado')

@php
    $distribuicao = $pagamento->distribuicao;
    $familia = $distribuicao?->familia;
    $cesta = $distribuicao?->cesta;
    $dataPagamento = $pagamento->dataPagamento();
@endphp

@section('content')
    <div class="max-w-xl mx-auto">
        <div class="bg-white rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] p-6 sm:p-8 text-center">
            <div class="mx-auto mb-5 flex items-center justify-center w-16 h-16 rounded-full bg-emerald-50">
                <svg class="w-9 h-9 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M20 6 9 17l-5-5" />
                </svg>
            </div>

            <h1 class="text-2xl font-semibold mb-1">Pagamento confirmado!</h1>
            <p class="text-[#706f6c] text-sm mb-6">O PIX da cesta foi recebido com sucesso.</p>

            <div class="rounded-md border border-[#e3e3e0] divide-y divide-[#e3e3e0] text-sm text-left mb-6">
                <div class="flex justify-between gap-4 px-4 py-3">
                    <span class="text-[#706f6c]">Valor pago</span>
                    <span class="font-semibold">R$ {{ number_format($pagamento->valor_reais, 2, ',', '.') }}</span>
                </div>
                @if ($cesta)
                    <div class="flex justify-between gap-4 px-4 py-3">
                        <span class="text-[#706f6c]">Cesta</span>
                        <span class="font-medium text-right">{{ $cesta->nome }}</span>
                    </div>
                @endif
                @if ($familia)
                    <div class="flex justify-between gap-4 px-4 py-3">
                        <span class="text-[#706f6c]">Família</span>
                        <span class="font-medium text-right">{{ $familia->nome_responsavel }}</span>
                    </div>
                @endif
                <div class="flex justify-between gap-4 px-4 py-3">
                    <span class="text-[#706f6c]">Data do pagamento</span>
                    <span>{{ $dataPagamento?->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i') }}</span>
                </div>
                <div class="flex justify-between gap-4 px-4 py-3">
                    <span class="text-[#706f6c]">Comprovante</span>
                    <span class="font-mono text-xs">Nº {{ $pagamento->numeroComprovante() }}</span>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 sm:justify-center">
                <a href="{{ route('pagamentos.comprovante', $pagamento) }}"
                   class="inline-flex justify-center px-4 py-2 text-sm bg-[#1b1b18] text-white rounded-sm hover:bg-black transition-colors">
                    Ver comprovante
                </a>
                <a href="{{ route('distribuicoes.index') }}"
                   class="inline-flex justify-center px-4 py-2 text-sm border border-[#e3e3e0] rounded-sm hover:border-[#1b1b18] transition-colors">
                    Voltar para distribuições
                </a>
            </div>
        </div>
    </div>
@endsection
