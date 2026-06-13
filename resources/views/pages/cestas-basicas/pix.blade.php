@extends('layouts.app')

@section('title', 'Pagamento PIX')

@section('content')
    <div class="mb-8 pb-6 border-b border-[#e3e3e0]">
        <h1 class="text-xl font-semibold">Pagamento PIX</h1>
        <p class="text-[#706f6c] text-sm">QR Code fictício para demonstração</p>
    </div>

    <div class="bg-white rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] p-6 flex flex-col items-center gap-4">
        <div class="bg-white p-4 rounded-md border border-[#e3e3e0]">
            <!-- QR code placeholder -->
            <svg role="img" aria-label="QR code PIX fictício" width="180" height="180" viewBox="0 0 180 180" xmlns="http://www.w3.org/2000/svg">
                <title>QR code PIX (fictício)</title>
                <rect width="100%" height="100%" fill="#fff"/>
                <g fill="#1b1b18">
                    <rect x="10" y="10" width="40" height="40"/>
                    <rect x="130" y="10" width="40" height="40"/>
                    <rect x="10" y="130" width="40" height="40"/>
                    <rect x="70" y="70" width="20" height="20"/>
                    <rect x="100" y="70" width="10" height="10"/>
                </g>
            </svg>
        </div>

        <div class="text-center">
            <p class="text-sm text-[#706f6c]">Chave PIX (fictícia):</p>
            <p class="font-mono mt-1">000e1111-2222-3333-4444-5555</p>
            <button type="button" aria-label="Copiar chave PIX" class="mt-3 px-4 py-1.5 bg-[#1b1b18] text-white rounded-sm text-sm">Copiar chave</button>
        </div>
    </div>
@endsection
