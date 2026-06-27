@extends('layouts.app')

@section('title', 'Painel do Gestor')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8 pb-6 border-b border-[#e3e3e0]">
        <div>
            <h1 class="text-xl font-semibold mb-1">Painel do Gestor</h1>
            <p class="text-[#706f6c] text-sm">Visão geral das firmas conveniadas</p>
        </div>
        <a href="{{ route('gestor.empresas.create') }}" class="inline-flex items-center justify-center gap-1.5 px-4 py-2 sm:py-1.5 bg-[#1b1b18] text-white rounded-sm text-sm font-medium hover:bg-black transition-colors">
            Nova firma
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        @foreach ([
            ['label' => 'Firmas cadastradas', 'value' => $stats['empresas'], 'color' => 'text-[#1b1b18]'],
            ['label' => 'Firmas ativas', 'value' => $stats['ativas'], 'color' => 'text-emerald-600'],
            ['label' => 'Pagamentos confirmados', 'value' => $stats['pagamentos_pagos'], 'color' => 'text-amber-600'],
        ] as $stat)
            <div class="bg-white rounded-lg p-5 shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)]">
                <p class="text-[#706f6c] text-sm mb-1">{{ $stat['label'] }}</p>
                <p class="text-2xl font-semibold {{ $stat['color'] }}">{{ $stat['value'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="bg-white rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)]">
        <div class="flex items-center justify-between px-5 pt-5 pb-3">
            <h2 class="text-sm font-semibold">Firmas recentes</h2>
            <a href="{{ route('gestor.empresas.index') }}" class="text-sm text-[#f53003] font-medium hover:underline">Ver todas</a>
        </div>
        <div class="app-table-wrap">
            <table class="app-table text-sm border-separate border-spacing-0">
                <thead>
                    <tr class="text-left text-[#706f6c]">
                        <th class="px-5 py-2 font-medium border-y border-[#e3e3e0]">Firma</th>
                        <th class="px-5 py-2 font-medium border-y border-[#e3e3e0]">Produtos</th>
                        <th class="px-5 py-2 font-medium border-y border-[#e3e3e0]">Status</th>
                        <th class="px-5 py-2 font-medium border-y border-[#e3e3e0] text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($empresas as $empresa)
                        <tr class="hover:bg-[#FDFDFC] transition-colors">
                            <td class="px-5 py-3 font-medium border-b border-[#e3e3e0]">{{ $empresa->nome_fantasia }}</td>
                            <td class="px-5 py-3 text-[#706f6c] border-b border-[#e3e3e0]">{{ $empresa->produtos_count }}</td>
                            <td class="px-5 py-3 border-b border-[#e3e3e0]">
                                <span @class([
                                    'text-xs font-medium px-2 py-0.5 rounded-sm',
                                    'bg-emerald-50 text-emerald-700' => $empresa->ativo,
                                    'bg-[#dbdbd7] text-[#706f6c]' => ! $empresa->ativo,
                                ])>{{ $empresa->ativo ? 'Ativa' : 'Inativa' }}</span>
                            </td>
                            <td class="px-5 py-3 text-right border-b border-[#e3e3e0]">
                                <a href="{{ route('gestor.empresas.show', $empresa) }}" class="px-2 py-1 text-xs border border-[#e3e3e0] rounded-sm hover:border-[#1b1b18] transition-colors">Ver</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-6 text-center text-[#706f6c] border-b border-[#e3e3e0]">Nenhuma firma cadastrada ainda.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
