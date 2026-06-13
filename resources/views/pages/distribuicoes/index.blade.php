@extends('layouts.app')

@section('title', 'Distribuições')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8 pb-6 border-b border-[#e3e3e0]">
        <div>
            <h1 class="text-xl font-semibold mb-1">Distribuições</h1>
            <p class="text-[#706f6c] text-sm">Histórico e registro de entregas de cestas</p>
        </div>
        <button type="button" data-dialog-open="modalDistribuicao"
                class="inline-flex items-center gap-1.5 px-4 py-1.5 bg-[#1b1b18] text-white rounded-sm text-sm font-medium hover:bg-black transition-colors">
            Nova distribuição
        </button>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        @foreach ([
            ['label' => 'Este mês', 'value' => '47', 'color' => 'text-emerald-600'],
            ['label' => 'Pendentes', 'value' => '5', 'color' => 'text-amber-600'],
            ['label' => 'Entregues', 'value' => '42', 'color' => 'text-[#1b1b18]'],
        ] as $stat)
            <div class="bg-white rounded-lg p-4 text-center shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)]">
                <p class="text-[#706f6c] text-sm mb-1">{{ $stat['label'] }}</p>
                <p class="text-2xl font-semibold {{ $stat['color'] }}">{{ $stat['value'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="bg-white rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] p-5">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-5">
            <input type="date" value="2026-06-01"
                   class="text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
            <select class="text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
                <option selected>Todos os status</option>
                <option>Pendente</option>
                <option>Entregue</option>
                <option>Cancelada</option>
            </select>
            <input type="search" placeholder="Buscar família..."
                   class="text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[#e3e3e0] text-left text-[#706f6c]">
                        <th class="pb-3 font-medium">#</th>
                        <th class="pb-3 font-medium">Data</th>
                        <th class="pb-3 font-medium">Família</th>
                        <th class="pb-3 font-medium">Itens</th>
                        <th class="pb-3 font-medium">Responsável</th>
                        <th class="pb-3 font-medium">Status</th>
                        <th class="pb-3 font-medium text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e3e3e0]">
                    @foreach ([
                        ['id' => 1042, 'data' => '10/06/2026', 'familia' => 'Maria Silva', 'itens' => '8 itens', 'resp' => 'Admin', 'status' => 'Entregue', 'badge' => 'bg-emerald-50 text-emerald-700'],
                        ['id' => 1041, 'data' => '09/06/2026', 'familia' => 'João Santos', 'itens' => '8 itens', 'resp' => 'Admin', 'status' => 'Pendente', 'badge' => 'bg-amber-50 text-amber-700'],
                        ['id' => 1040, 'data' => '08/06/2026', 'familia' => 'Ana Oliveira', 'itens' => '7 itens', 'resp' => 'Admin', 'status' => 'Entregue', 'badge' => 'bg-emerald-50 text-emerald-700'],
                        ['id' => 1039, 'data' => '07/06/2026', 'familia' => 'Carlos Pereira', 'itens' => '8 itens', 'resp' => 'Admin', 'status' => 'Entregue', 'badge' => 'bg-emerald-50 text-emerald-700'],
                        ['id' => 1038, 'data' => '06/06/2026', 'familia' => 'Fernanda Costa', 'itens' => '8 itens', 'resp' => 'Admin', 'status' => 'Cancelada', 'badge' => 'bg-red-50 text-[#f53003]'],
                    ] as $row)
                        <tr class="hover:bg-[#FDFDFC]">
                            <td class="py-3 text-[#706f6c]">{{ $row['id'] }}</td>
                            <td class="py-3">{{ $row['data'] }}</td>
                            <td class="py-3 font-medium">{{ $row['familia'] }}</td>
                            <td class="py-3 text-[#706f6c]">{{ $row['itens'] }}</td>
                            <td class="py-3 text-[#706f6c]">{{ $row['resp'] }}</td>
                            <td class="py-3">
                                <span class="inline-flex px-2 py-0.5 rounded-sm text-xs font-medium {{ $row['badge'] }}">{{ $row['status'] }}</span>
                            </td>
                            <td class="py-3 text-right">
                                <button class="px-2 py-1 text-xs border border-[#e3e3e0] rounded-sm hover:border-[#1b1b18] transition-colors">Detalhes</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('modals')
    <dialog id="modalDistribuicao" data-form-dialog
            class="backdrop:bg-black/40 bg-transparent p-0 max-w-lg w-full rounded-lg">
        <div class="bg-white rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] p-6">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-base font-semibold">Registrar distribuição</h2>
                <button type="button" data-dialog-close class="text-[#706f6c] hover:text-[#1b1b18] text-xl leading-none">&times;</button>
            </div>
            <form class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="familiaDistribuicao" class="block text-sm font-medium mb-1">Família</label>
                        <select id="familiaDistribuicao" required class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
                            <option value="">Selecione a família...</option>
                            <option>Maria Silva — Centro</option>
                            <option>João Santos — Jardim Primavera</option>
                            <option>Ana Oliveira — Vila Nova</option>
                            <option>Fernanda Costa — Centro</option>
                        </select>
                    </div>
                    <div>
                        <label for="dataDistribuicao" class="block text-sm font-medium mb-1">Data da entrega</label>
                        <input type="date" id="dataDistribuicao" required
                               class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
                    </div>
                </div>
                <div>
                    <p class="block text-sm font-medium mb-2">Itens da cesta</p>
                    <div class="border border-[#e3e3e0] rounded-sm p-4 bg-[#FDFDFC] space-y-2 text-sm text-[#706f6c]">
                        @foreach ([
                            'Arroz branco 5kg (1x)',
                            'Feijão carioca 1kg (2x)',
                            'Óleo de soja 900ml (1x)',
                            'Açúcar cristal 1kg (1x)',
                            'Macarrão espaguete 500g (2x)',
                            'Sabão em barra (4x)',
                        ] as $item)
                            <label class="flex items-center gap-2">
                                <input type="checkbox" checked disabled class="rounded border-[#e3e3e0]">
                                {{ $item }}
                            </label>
                        @endforeach
                        <p class="text-xs pt-1">Composição padrão da cesta básica</p>
                    </div>
                </div>
                <div>
                    <label for="obsDistribuicao" class="block text-sm font-medium mb-1">Observações</label>
                    <textarea id="obsDistribuicao" rows="2" placeholder="Informações adicionais sobre a entrega..."
                              class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" data-dialog-close class="px-4 py-1.5 text-sm border border-[#e3e3e0] rounded-sm hover:border-[#1b1b18] transition-colors">Cancelar</button>
                    <button type="submit" class="px-4 py-1.5 text-sm bg-[#1b1b18] text-white rounded-sm hover:bg-black transition-colors">Registrar</button>
                </div>
            </form>
        </div>
    </dialog>
@endpush
