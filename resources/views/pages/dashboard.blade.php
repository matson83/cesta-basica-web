@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8 pb-6 border-b border-[#e3e3e0]">
        <div>
            <h1 class="text-xl font-semibold mb-1">Dashboard</h1>
            <p class="text-[#706f6c] text-sm">Visão geral da distribuição de cestas básicas</p>
        </div>
        <a href="{{ route('distribuicoes.index') }}" class="inline-flex items-center gap-1.5 px-4 py-1.5 bg-[#1b1b18] text-white rounded-sm text-sm font-medium hover:bg-black transition-colors">
            Nova distribuição
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-8">
        @foreach ([
            ['label' => 'Famílias cadastradas', 'value' => '128', 'color' => 'text-emerald-600'],
            ['label' => 'Cestas distribuídas', 'value' => '342', 'color' => 'text-[#1b1b18]'],
            ['label' => 'Produtos cadastrados', 'value' => '24', 'color' => 'text-amber-600'],
            ['label' => 'Estoque baixo', 'value' => '3', 'color' => 'text-[#f53003]'],
        ] as $stat)
            <div class="bg-white rounded-lg p-5 shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] hover:shadow-md transition-shadow">
                <p class="text-[#706f6c] text-sm mb-1">{{ $stat['label'] }}</p>
                <p class="text-2xl font-semibold {{ $stat['color'] }}">{{ $stat['value'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
        <div class="lg:col-span-3 bg-white rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)]">
            <div class="px-5 pt-5 pb-3">
                <h2 class="text-sm font-semibold">Últimas distribuições</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-t border-[#e3e3e0] text-left text-[#706f6c]">
                            <th class="px-5 py-2 font-medium">Data</th>
                            <th class="px-5 py-2 font-medium">Família</th>
                            <th class="px-5 py-2 font-medium">Itens</th>
                            <th class="px-5 py-2 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#e3e3e0]">
                        @foreach ([
                            ['data' => '10/06/2026', 'familia' => 'Maria Silva', 'itens' => 8, 'status' => 'Entregue', 'badge' => 'bg-emerald-50 text-emerald-700'],
                            ['data' => '09/06/2026', 'familia' => 'João Santos', 'itens' => 8, 'status' => 'Pendente', 'badge' => 'bg-amber-50 text-amber-700'],
                            ['data' => '08/06/2026', 'familia' => 'Ana Oliveira', 'itens' => 7, 'status' => 'Entregue', 'badge' => 'bg-emerald-50 text-emerald-700'],
                            ['data' => '07/06/2026', 'familia' => 'Carlos Pereira', 'itens' => 8, 'status' => 'Entregue', 'badge' => 'bg-emerald-50 text-emerald-700'],
                        ] as $row)
                            <tr class="hover:bg-[#FDFDFC]">
                                <td class="px-5 py-3">{{ $row['data'] }}</td>
                                <td class="px-5 py-3 font-medium">{{ $row['familia'] }}</td>
                                <td class="px-5 py-3">{{ $row['itens'] }}</td>
                                <td class="px-5 py-3">
                                    <span class="inline-flex px-2 py-0.5 rounded-sm text-xs font-medium {{ $row['badge'] }}">{{ $row['status'] }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-4 text-right border-t border-[#e3e3e0]">
                <a href="{{ route('distribuicoes.index') }}" class="text-sm text-[#f53003] font-medium hover:underline">Ver todas</a>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)]">
                <div class="px-5 pt-5 pb-3">
                    <h2 class="text-sm font-semibold">Produtos com estoque baixo</h2>
                </div>
                <ul class="divide-y divide-[#e3e3e0] border-t border-[#e3e3e0]">
                    @foreach ([
                        ['nome' => 'Óleo de soja 900ml', 'qtd' => '12 un.', 'danger' => true],
                        ['nome' => 'Café 500g', 'qtd' => '8 un.', 'danger' => true],
                        ['nome' => 'Leite em pó 400g', 'qtd' => '18 un.', 'danger' => false],
                    ] as $produto)
                        <li class="flex items-center justify-between px-5 py-3 text-sm">
                            <span>{{ $produto['nome'] }}</span>
                            <span @class([
                                'text-xs font-medium px-2 py-0.5 rounded-sm',
                                'bg-red-50 text-[#f53003]' => $produto['danger'],
                                'bg-amber-50 text-amber-700' => ! $produto['danger'],
                            ])>{{ $produto['qtd'] }}</span>
                        </li>
                    @endforeach
                </ul>
                <div class="px-5 py-4 text-right border-t border-[#e3e3e0]">
                    <a href="{{ route('produtos.index') }}" class="text-sm text-[#f53003] font-medium hover:underline">Gerenciar produtos</a>
                </div>
            </div>

            <div class="bg-[#fff2f2] rounded-lg p-5 shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)]">
                <h2 class="text-sm font-semibold text-[#f53003] mb-3">Acesso rápido</h2>
                <div class="flex flex-col gap-2">
                    <a href="{{ route('familias.index') }}" class="inline-flex justify-center px-4 py-1.5 bg-[#1b1b18] text-white rounded-sm text-sm font-medium hover:bg-black transition-colors">
                        Cadastrar família
                    </a>
                    <a href="{{ route('produtos.index') }}" class="inline-flex justify-center px-4 py-1.5 border border-[#19140035] text-[#1b1b18] rounded-sm text-sm hover:border-black transition-colors">
                        Adicionar produto
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
