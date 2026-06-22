@extends('layouts.app')

@section('title', 'Distribuições')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8 pb-6 border-b border-[#e3e3e0]">
        <div>
            <h1 class="text-xl font-semibold mb-1">Distribuições</h1>
            <p class="text-[#706f6c] text-sm">Histórico e registro de entregas de cestas</p>
        </div>
        <button type="button" data-dialog-open="modalDistribuicao"
                class="inline-flex items-center justify-center gap-1.5 px-4 py-2 sm:py-1.5 bg-[#1b1b18] text-white rounded-sm text-sm font-medium hover:bg-black transition-colors">
            Nova distribuição
        </button>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        @foreach ([
            ['label' => 'Este mês', 'value' => $stats['mes'], 'color' => 'text-emerald-600'],
            ['label' => 'Pendentes', 'value' => $stats['pendentes'], 'color' => 'text-amber-600'],
            ['label' => 'Pagas', 'value' => $stats['pagas'], 'color' => 'text-[#1b1b18]'],
        ] as $stat)
            <div class="bg-white rounded-lg p-4 text-center shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)]">
                <p class="text-[#706f6c] text-sm mb-1">{{ $stat['label'] }}</p>
                <p class="text-2xl font-semibold {{ $stat['color'] }}">{{ $stat['value'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="bg-white rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] p-4 sm:p-5">
        <form method="GET" action="{{ route('distribuicoes.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-3 mb-5">
            <input type="date" name="data" value="{{ request('data') }}"
                   class="text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
            <select name="status" class="text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
                <option value="">Todos os status</option>
                @foreach (\App\Models\Distribuicao::statusOpcoes() as $value => $label)
                    <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <input type="search" name="busca" value="{{ request('busca') }}" placeholder="Buscar família..."
                   data-table-search="#tabelaDistribuicoes"
                   class="md:col-span-2 text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
            <div class="flex flex-col sm:flex-row gap-2">
                <button type="submit" class="inline-flex justify-center px-4 py-2 text-sm bg-[#1b1b18] text-white rounded-sm hover:bg-black transition-colors">Filtrar</button>
                <a href="{{ route('distribuicoes.index') }}" class="inline-flex justify-center px-4 py-2 text-sm border border-[#e3e3e0] rounded-sm hover:border-[#1b1b18] transition-colors">Limpar</a>
            </div>
        </form>

        <div class="app-table-wrap">
            <table id="tabelaDistribuicoes" class="app-table app-table--fluid text-sm">
                <colgroup>
                    <col style="width: 3rem">
                    <col style="width: 6.5rem">
                    <col style="width: 24%">
                    <col style="width: 5.5rem">
                    <col style="width: 16%">
                    <col style="width: 6.5rem">
                    <col style="width: 26%">
                </colgroup>
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
                        <tr class="hover:bg-[#FDFDFC]"
                            data-search-row="{{ $distribuicao->familia?->nome_responsavel }} {{ $distribuicao->familia?->bairro }} {{ $distribuicao->responsavel }}">
                            <td class="py-3 text-[#706f6c]">{{ $distribuicao->id }}</td>
                            <td class="py-3 whitespace-nowrap">{{ $distribuicao->data_entrega->format('d/m/Y') }}</td>
                            <td class="py-3 font-medium app-table__truncate" title="{{ $distribuicao->familia?->nome_responsavel ?? 'Família removida' }}">{{ $distribuicao->familia?->nome_responsavel ?? 'Família removida' }}</td>
                            <td class="py-3 text-[#706f6c] whitespace-nowrap">{{ $distribuicao->cesta?->total_itens ?? 0 }} itens</td>
                            <td class="py-3 text-[#706f6c] app-table__truncate" title="{{ $distribuicao->responsavel ?? 'Não informado' }}">{{ $distribuicao->responsavel ?? 'Não informado' }}</td>
                            <td class="py-3 whitespace-nowrap">
                                <span @class([
                                    'inline-flex px-2 py-0.5 rounded-sm text-xs font-medium',
                                    'bg-emerald-50 text-emerald-700' => $distribuicao->isPago(),
                                    'bg-amber-50 text-amber-700' => $distribuicao->isPendente(),
                                    'bg-red-50 text-[#f53003]' => $distribuicao->isCancelado(),
                                ])>{{ $distribuicao->statusLabel() }}</span>
                            </td>
                            <td class="py-3 text-right app-table__actions">
                                <div class="app-table-actions">
                                    @if ($distribuicao->pagamento && $distribuicao->pagamento->status === \App\Models\Pagamento::STATUS_PAGO)
                                        <a href="{{ route('pagamentos.comprovante', $distribuicao->pagamento) }}" class="px-2 py-1 text-xs border border-[#e3e3e0] rounded-sm hover:border-[#1b1b18] transition-colors">Comprovante</a>
                                    @elseif ($distribuicao->cesta)
                                        <form action="{{ route('pagamentos.pagar', $distribuicao) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-2 py-1 text-xs bg-[#1b1b18] text-white rounded-sm hover:bg-black transition-colors">
                                                {{ $distribuicao->pagamento ? 'Continuar PIX' : 'Pagar' }}
                                            </button>
                                        </form>
                                    @endif
                                    <button type="button" data-dialog-open="modalEditarDistribuicao{{ $distribuicao->id }}"
                                            class="px-2 py-1 text-xs border border-[#e3e3e0] rounded-sm hover:border-[#1b1b18] transition-colors">
                                        Editar
                                    </button>
                                    <form id="form-remover-distribuicao-{{ $distribuicao->id }}" action="{{ route('distribuicoes.destroy', $distribuicao) }}" method="POST" class="contents">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                                data-confirm-delete
                                                data-confirm-form="form-remover-distribuicao-{{ $distribuicao->id }}"
                                                data-confirm-title="Excluir distribuição"
                                                data-confirm-message="Deseja realmente excluir a distribuição #{{ $distribuicao->id }} de {{ $distribuicao->familia?->nome_responsavel ?? 'família removida' }}? Esta ação não pode ser desfeita."
                                                class="px-2 py-1 text-xs border border-[#e3e3e0] rounded-sm hover:border-[#1b1b18] transition-colors">
                                            Remover
                                        </button>
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
    <dialog id="modalDistribuicao" data-form-dialog @if ($errors->any() && old('form_context') === 'distribuicao-store') data-reopen="true" @endif
            class="app-dialog backdrop:bg-black/40 bg-transparent p-0 rounded-lg" style="--dialog-width: 32rem">
        <div class="bg-white rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] p-4 sm:p-6">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-base font-semibold">Registrar distribuição</h2>
                <button type="button" data-dialog-close class="text-[#706f6c] hover:text-[#1b1b18] text-xl leading-none">&times;</button>
            </div>
            <form action="{{ route('distribuicoes.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="form_context" value="distribuicao-store">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="familiaDistribuicao" class="block text-sm font-medium mb-1">Família</label>
                        <select id="familiaDistribuicao" name="familia_id" required class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
                            <option value="">Selecione a família...</option>
                            @foreach ($familias as $familia)
                                <option value="{{ $familia->id }}" @selected(old('form_context') === 'distribuicao-store' && old('familia_id') == $familia->id)>{{ $familia->nome_responsavel }} - {{ $familia->bairro }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="dataDistribuicao" class="block text-sm font-medium mb-1">Data da entrega</label>
                        <input type="date" id="dataDistribuicao" name="data_entrega" value="{{ old('form_context') === 'distribuicao-store' ? old('data_entrega', now()->toDateString()) : now()->toDateString() }}" required
                               class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="cestaDistribuicao" class="block text-sm font-medium mb-1">Cesta</label>
                        <select id="cestaDistribuicao" name="cesta_id" class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
                            <option value="">Selecione a cesta...</option>
                            @foreach ($cestas as $cesta)
                                <option value="{{ $cesta->id }}" @selected(old('form_context') === 'distribuicao-store' && old('cesta_id') == $cesta->id)>{{ $cesta->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="statusDistribuicao" class="block text-sm font-medium mb-1">Status</label>
                        <select id="statusDistribuicao" name="status" required class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
                            @foreach (\App\Models\Distribuicao::statusOpcoes() as $value => $label)
                                <option value="{{ $value }}" @selected(old('form_context') === 'distribuicao-store' && old('status', 'pendente') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label for="responsavelDistribuicao" class="block text-sm font-medium mb-1">Responsável</label>
                    <input type="text" id="responsavelDistribuicao" name="responsavel" value="{{ old('form_context') === 'distribuicao-store' ? old('responsavel') : '' }}" placeholder="Nome de quem registrou a entrega"
                           class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
                </div>
                <div>
                    <label for="obsDistribuicao" class="block text-sm font-medium mb-1">Observações</label>
                    <textarea id="obsDistribuicao" name="observacoes" rows="2" placeholder="Informações adicionais sobre a entrega..."
                              class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">{{ old('form_context') === 'distribuicao-store' ? old('observacoes') : '' }}</textarea>
                </div>
                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-2 pt-2">
                    <button type="button" data-dialog-close class="px-4 py-2 sm:py-1.5 text-sm border border-[#e3e3e0] rounded-sm hover:border-[#1b1b18] transition-colors">Cancelar</button>
                    <button type="submit" class="px-4 py-2 sm:py-1.5 text-sm bg-[#1b1b18] text-white rounded-sm hover:bg-black transition-colors">Registrar</button>
                </div>
            </form>
        </div>
    </dialog>

    @foreach ($distribuicoes as $distribuicao)
        @php
            $distribuicaoEditContext = 'distribuicao-update-'.$distribuicao->id;
            $editFamiliaId = old('form_context') === $distribuicaoEditContext ? old('familia_id') : $distribuicao->familia_id;
            $editCestaId = old('form_context') === $distribuicaoEditContext ? old('cesta_id') : $distribuicao->cesta_id;
            $editStatus = old('form_context') === $distribuicaoEditContext
                ? \App\Models\Distribuicao::normalizeStatus(old('status'))
                : \App\Models\Distribuicao::normalizeStatus($distribuicao->status);
        @endphp
        <dialog id="modalEditarDistribuicao{{ $distribuicao->id }}" data-form-dialog @if ($errors->any() && old('form_context') === $distribuicaoEditContext) data-reopen="true" @endif
                class="app-dialog backdrop:bg-black/40 bg-transparent p-0 rounded-lg" style="--dialog-width: 32rem">
            <div class="bg-white rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] p-4 sm:p-6">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-base font-semibold">Editar distribuição</h2>
                    <button type="button" data-dialog-close class="text-[#706f6c] hover:text-[#1b1b18] text-xl leading-none">&times;</button>
                </div>
                <form action="{{ route('distribuicoes.update', $distribuicao) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="form_context" value="{{ $distribuicaoEditContext }}">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="familiaDistribuicao{{ $distribuicao->id }}" class="block text-sm font-medium mb-1">Família</label>
                            <select id="familiaDistribuicao{{ $distribuicao->id }}" name="familia_id" required class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
                                <option value="">Selecione a família...</option>
                                @if ($distribuicao->familia && ! $familias->contains('id', $distribuicao->familia_id))
                                    <option value="{{ $distribuicao->familia_id }}" @selected($editFamiliaId == $distribuicao->familia_id)>{{ $distribuicao->familia->nome_responsavel }} - {{ $distribuicao->familia->bairro }}</option>
                                @endif
                                @foreach ($familias as $familia)
                                    <option value="{{ $familia->id }}" @selected($editFamiliaId == $familia->id)>{{ $familia->nome_responsavel }} - {{ $familia->bairro }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="dataDistribuicao{{ $distribuicao->id }}" class="block text-sm font-medium mb-1">Data da entrega</label>
                            <input type="date" id="dataDistribuicao{{ $distribuicao->id }}" name="data_entrega" value="{{ old('form_context') === $distribuicaoEditContext ? old('data_entrega') : $distribuicao->data_entrega->format('Y-m-d') }}" required
                                   class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="cestaDistribuicao{{ $distribuicao->id }}" class="block text-sm font-medium mb-1">Cesta</label>
                            <select id="cestaDistribuicao{{ $distribuicao->id }}" name="cesta_id" class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
                                <option value="">Selecione a cesta...</option>
                                @if ($distribuicao->cesta && ! $cestas->contains('id', $distribuicao->cesta_id))
                                    <option value="{{ $distribuicao->cesta_id }}" @selected($editCestaId == $distribuicao->cesta_id)>{{ $distribuicao->cesta->nome }}</option>
                                @endif
                                @foreach ($cestas as $cesta)
                                    <option value="{{ $cesta->id }}" @selected($editCestaId == $cesta->id)>{{ $cesta->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="statusDistribuicao{{ $distribuicao->id }}" class="block text-sm font-medium mb-1">Status</label>
                            <select id="statusDistribuicao{{ $distribuicao->id }}" name="status" required class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
                                @foreach (\App\Models\Distribuicao::statusOpcoes() as $value => $label)
                                    <option value="{{ $value }}" @selected($editStatus === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label for="responsavelDistribuicao{{ $distribuicao->id }}" class="block text-sm font-medium mb-1">Responsável</label>
                        <input type="text" id="responsavelDistribuicao{{ $distribuicao->id }}" name="responsavel" value="{{ old('form_context') === $distribuicaoEditContext ? old('responsavel') : $distribuicao->responsavel }}" placeholder="Nome de quem registrou a entrega"
                               class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
                    </div>
                    <div>
                        <label for="obsDistribuicao{{ $distribuicao->id }}" class="block text-sm font-medium mb-1">Observações</label>
                        <textarea id="obsDistribuicao{{ $distribuicao->id }}" name="observacoes" rows="2" placeholder="Informações adicionais sobre a entrega..."
                                  class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">{{ old('form_context') === $distribuicaoEditContext ? old('observacoes') : $distribuicao->observacoes }}</textarea>
                    </div>
                    <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-2 pt-2">
                        <button type="button" data-dialog-close class="px-4 py-2 sm:py-1.5 text-sm border border-[#e3e3e0] rounded-sm hover:border-[#1b1b18] transition-colors">Cancelar</button>
                        <button type="submit" class="px-4 py-2 sm:py-1.5 text-sm bg-[#1b1b18] text-white rounded-sm hover:bg-black transition-colors">Salvar alterações</button>
                    </div>
                </form>
            </div>
        </dialog>
    @endforeach
@endpush
