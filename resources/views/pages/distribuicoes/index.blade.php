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
            ['label' => 'Este mês', 'value' => $stats['mes'], 'color' => 'text-emerald-600'],
            ['label' => 'Pendentes', 'value' => $stats['pendentes'], 'color' => 'text-amber-600'],
            ['label' => 'Entregues', 'value' => $stats['entregues'], 'color' => 'text-[#1b1b18]'],
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
                    @forelse ($distribuicoes as $distribuicao)
                        <tr class="hover:bg-[#FDFDFC]">
                            <td class="py-3 text-[#706f6c]">{{ $distribuicao->id }}</td>
                            <td class="py-3">{{ $distribuicao->data_entrega->format('d/m/Y') }}</td>
                            <td class="py-3 font-medium">{{ $distribuicao->familia?->nome_responsavel }}</td>
                            <td class="py-3 text-[#706f6c]">{{ $distribuicao->cesta?->total_itens ?? 0 }} itens</td>
                            <td class="py-3 text-[#706f6c]">{{ $distribuicao->responsavel ?? '—' }}</td>
                            <td class="py-3">
                                <span @class([
                                    'inline-flex px-2 py-0.5 rounded-sm text-xs font-medium',
                                    'bg-emerald-50 text-emerald-700' => in_array($distribuicao->status, [\App\Models\Distribuicao::STATUS_ENTREGUE, \App\Models\Distribuicao::STATUS_PAGA]),
                                    'bg-amber-50 text-amber-700' => $distribuicao->status === \App\Models\Distribuicao::STATUS_PENDENTE,
                                    'bg-red-50 text-[#f53003]' => $distribuicao->status === \App\Models\Distribuicao::STATUS_CANCELADA,
                                ])>{{ ucfirst($distribuicao->status) }}</span>
                            </td>
                            <td class="py-3 text-right">
                                <div class="inline-flex items-center gap-1">
                                    @if ($distribuicao->pagamento && $distribuicao->pagamento->status === \App\Models\Pagamento::STATUS_PAGO)
                                        <a href="{{ route('pagamentos.pix', $distribuicao->pagamento) }}" class="px-2 py-1 text-xs border border-[#e3e3e0] rounded-sm hover:border-[#1b1b18] transition-colors">Ver PIX</a>
                                    @elseif ($distribuicao->cesta)
                                        <form action="{{ route('pagamentos.pagar', $distribuicao) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2 py-1 text-xs bg-[#1b1b18] text-white rounded-sm hover:bg-black transition-colors">
                                                {{ $distribuicao->pagamento ? 'Continuar PIX' : 'Pagar' }}
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('distribuicoes.destroy', $distribuicao) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Remover esta distribuição?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2 py-1 text-xs border border-[#e3e3e0] rounded-sm hover:border-[#1b1b18] transition-colors">Remover</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-6 text-center text-[#706f6c]">Nenhuma distribuição registrada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('modals')
    <dialog id="modalDistribuicao" data-form-dialog @if ($errors->any() && old('familia_id')) data-reopen="true" @endif
            class="backdrop:bg-black/40 bg-transparent p-0 max-w-lg w-full rounded-lg">
        <div class="bg-white rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] p-6">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-base font-semibold">Registrar distribuição</h2>
                <button type="button" data-dialog-close class="text-[#706f6c] hover:text-[#1b1b18] text-xl leading-none">&times;</button>
            </div>
            <form action="{{ route('distribuicoes.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="familiaDistribuicao" class="block text-sm font-medium mb-1">Família</label>
                        <select id="familiaDistribuicao" name="familia_id" required class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
                            <option value="">Selecione a família...</option>
                            @foreach ($familias as $familia)
                                <option value="{{ $familia->id }}" @selected(old('familia_id') == $familia->id)>{{ $familia->nome_responsavel }} — {{ $familia->bairro }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="dataDistribuicao" class="block text-sm font-medium mb-1">Data da entrega</label>
                        <input type="date" id="dataDistribuicao" name="data_entrega" value="{{ old('data_entrega') }}" required
                               class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="cestaDistribuicao" class="block text-sm font-medium mb-1">Cesta</label>
                        <select id="cestaDistribuicao" name="cesta_id" class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
                            <option value="">Selecione a cesta...</option>
                            @foreach ($cestas as $cesta)
                                <option value="{{ $cesta->id }}" @selected(old('cesta_id') == $cesta->id)>{{ $cesta->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="statusDistribuicao" class="block text-sm font-medium mb-1">Status</label>
                        <select id="statusDistribuicao" name="status" required class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
                            @foreach (['pendente' => 'Pendente', 'entregue' => 'Entregue', 'cancelada' => 'Cancelada'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('status') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label for="responsavelDistribuicao" class="block text-sm font-medium mb-1">Responsável</label>
                    <input type="text" id="responsavelDistribuicao" name="responsavel" value="{{ old('responsavel') }}" placeholder="Nome de quem registrou a entrega"
                           class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
                </div>
                <div>
                    <label for="obsDistribuicao" class="block text-sm font-medium mb-1">Observações</label>
                    <textarea id="obsDistribuicao" name="observacoes" rows="2" placeholder="Informações adicionais sobre a entrega..."
                              class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">{{ old('observacoes') }}</textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" data-dialog-close class="px-4 py-1.5 text-sm border border-[#e3e3e0] rounded-sm hover:border-[#1b1b18] transition-colors">Cancelar</button>
                    <button type="submit" class="px-4 py-1.5 text-sm bg-[#1b1b18] text-white rounded-sm hover:bg-black transition-colors">Registrar</button>
                </div>
            </form>
        </div>
    </dialog>
@endpush
