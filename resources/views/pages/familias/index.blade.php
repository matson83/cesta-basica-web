@extends('layouts.app')

@section('title', 'Famílias')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8 pb-6 border-b border-[#e3e3e0]">
        <div>
            <h1 class="text-xl font-semibold mb-1">Famílias</h1>
            <p class="text-[#706f6c] text-sm">Beneficiários cadastrados no programa</p>
        </div>
        <button type="button" data-dialog-open="modalFamilia"
                class="inline-flex items-center justify-center gap-1.5 px-4 py-2 sm:py-1.5 bg-[#1b1b18] text-white rounded-sm text-sm font-medium hover:bg-black transition-colors">
            Nova família
        </button>
    </div>

    <div class="bg-white rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] p-4 sm:p-5">
        <form method="GET" action="{{ route('familias.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-5">
            <div class="md:col-span-2">
                <input type="search" name="busca" value="{{ request('busca') }}" placeholder="Buscar por nome ou CPF..."
                       class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
            </div>
            <select name="ativo" class="text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
                <option value="">Todos os status</option>
                <option value="1" @selected(request('ativo') === '1')>Ativo</option>
                <option value="0" @selected(request('ativo') === '0')>Inativo</option>
            </select>
            <div class="flex flex-col sm:flex-row gap-2">
                <button type="submit" class="inline-flex justify-center px-4 py-2 text-sm bg-[#1b1b18] text-white rounded-sm hover:bg-black transition-colors">Filtrar</button>
                <a href="{{ route('familias.index') }}" class="inline-flex justify-center px-4 py-2 text-sm border border-[#e3e3e0] rounded-sm hover:border-[#1b1b18] transition-colors">Limpar</a>
            </div>
        </form>

        <div class="app-table-wrap">
            <table class="app-table text-sm" style="--table-min-width: 58rem">
                <thead>
                    <tr class="border-b border-[#e3e3e0] text-left text-[#706f6c]">
                        <th class="pb-3 font-medium">Responsável</th>
                        <th class="pb-3 font-medium">CPF</th>
                        <th class="pb-3 font-medium">Membros</th>
                        <th class="pb-3 font-medium">Bairro</th>
                        <th class="pb-3 font-medium">Telefone</th>
                        <th class="pb-3 font-medium">Status</th>
                        <th class="pb-3 font-medium text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e3e3e0]">
                    @forelse ($familias as $familia)
                        <tr class="hover:bg-[#FDFDFC]">
                            <td class="py-3 font-medium">{{ $familia->nome_responsavel }}</td>
                            <td class="py-3 text-[#706f6c]">{{ $familia->cpf }}</td>
                            <td class="py-3">{{ $familia->num_membros }}</td>
                            <td class="py-3 text-[#706f6c]">{{ $familia->bairro ?? 'Não informado' }}</td>
                            <td class="py-3 text-[#706f6c]">{{ $familia->telefone ?? 'Não informado' }}</td>
                            <td class="py-3">
                                <span @class([
                                    'text-xs font-medium px-2 py-0.5 rounded-sm',
                                    'bg-emerald-50 text-emerald-700' => $familia->ativo,
                                    'bg-[#dbdbd7] text-[#706f6c]' => ! $familia->ativo,
                                ])>{{ $familia->ativo ? 'Ativo' : 'Inativo' }}</span>
                            </td>
                            <td class="py-3 text-right">
                                <div class="app-table-actions">
                                    <button type="button" data-dialog-open="modalEditarFamilia{{ $familia->id }}"
                                            class="px-2 py-1 text-xs border border-[#e3e3e0] rounded-sm hover:border-[#1b1b18] transition-colors">
                                        Editar
                                    </button>
                                    <form id="form-remover-familia-{{ $familia->id }}" action="{{ route('familias.destroy', $familia) }}" method="POST" class="contents">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                                data-confirm-delete
                                                data-confirm-form="form-remover-familia-{{ $familia->id }}"
                                                data-confirm-title="Excluir família"
                                                data-confirm-message="Deseja realmente excluir a família &quot;{{ $familia->nome_responsavel }}&quot;? Esta ação não pode ser desfeita."
                                                class="px-2 py-1 text-xs border border-[#e3e3e0] rounded-sm hover:border-[#1b1b18] transition-colors">
                                            Remover
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-6 text-center text-[#706f6c]">Nenhuma família cadastrada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-5 pt-4 border-t border-[#e3e3e0] overflow-x-auto">
            {{ $familias->links() }}
        </div>
    </div>
@endsection

@push('modals')
    <dialog id="modalFamilia" data-form-dialog @if ($errors->any() && old('form_context') === 'familia-store') data-reopen="true" @endif
            class="app-dialog backdrop:bg-black/40 bg-transparent p-0 rounded-lg" style="--dialog-width: 32rem">
        <div class="bg-white rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] p-4 sm:p-6">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-base font-semibold">Cadastrar família</h2>
                <button type="button" data-dialog-close class="text-[#706f6c] hover:text-[#1b1b18] text-xl leading-none">&times;</button>
            </div>
            <form action="{{ route('familias.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="form_context" value="familia-store">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="sm:col-span-2">
                        <label for="nomeResponsavel" class="block text-sm font-medium mb-1">Nome do responsável</label>
                        <input type="text" id="nomeResponsavel" name="nome_responsavel" value="{{ old('form_context') === 'familia-store' ? old('nome_responsavel') : '' }}" required
                               class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
                    </div>
                    <div>
                        <label for="cpfResponsavel" class="block text-sm font-medium mb-1">CPF</label>
                        <input type="text" id="cpfResponsavel" name="cpf" value="{{ old('form_context') === 'familia-store' ? old('cpf') : '' }}" placeholder="000.000.000-00" required
                               class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
                    </div>
                    <div>
                        <label for="membrosFamilia" class="block text-sm font-medium mb-1">Nº de membros</label>
                        <input type="number" id="membrosFamilia" name="num_membros" min="1" value="{{ old('form_context') === 'familia-store' ? old('num_membros', 1) : 1 }}" required
                               class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
                    </div>
                    <div>
                        <label for="telefoneFamilia" class="block text-sm font-medium mb-1">Telefone</label>
                        <input type="tel" id="telefoneFamilia" name="telefone" value="{{ old('form_context') === 'familia-store' ? old('telefone') : '' }}" placeholder="(00) 00000-0000"
                               class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
                    </div>
                    <div>
                        <label for="bairroFamilia" class="block text-sm font-medium mb-1">Bairro</label>
                        <input type="text" id="bairroFamilia" name="bairro" value="{{ old('form_context') === 'familia-store' ? old('bairro') : '' }}"
                               class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
                    </div>
                    <div class="sm:col-span-3">
                        <label for="enderecoFamilia" class="block text-sm font-medium mb-1">Endereço completo</label>
                        <textarea id="enderecoFamilia" name="endereco" rows="2"
                                  class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">{{ old('form_context') === 'familia-store' ? old('endereco') : '' }}</textarea>
                    </div>
                    <label class="sm:col-span-3 flex items-center gap-2 text-sm">
                        <input type="hidden" name="ativo" value="0">
                        <input type="checkbox" name="ativo" value="1" class="rounded-sm border-[#e3e3e0]" @checked(old('form_context') !== 'familia-store' || (bool) old('ativo', true))>
                        Família ativa
                    </label>
                </div>
                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-2 pt-2">
                    <button type="button" data-dialog-close class="px-4 py-2 sm:py-1.5 text-sm border border-[#e3e3e0] rounded-sm hover:border-[#1b1b18] transition-colors">Cancelar</button>
                    <button type="submit" class="px-4 py-2 sm:py-1.5 text-sm bg-[#1b1b18] text-white rounded-sm hover:bg-black transition-colors">Salvar</button>
                </div>
            </form>
        </div>
    </dialog>

    @foreach ($familias as $familia)
        @php
            $familiaEditContext = 'familia-update-'.$familia->id;
            $familiaAtiva = old('form_context') === $familiaEditContext ? (bool) old('ativo') : $familia->ativo;
        @endphp
        <dialog id="modalEditarFamilia{{ $familia->id }}" data-form-dialog @if ($errors->any() && old('form_context') === $familiaEditContext) data-reopen="true" @endif
                class="app-dialog backdrop:bg-black/40 bg-transparent p-0 rounded-lg" style="--dialog-width: 32rem">
            <div class="bg-white rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] p-4 sm:p-6">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-base font-semibold">Editar família</h2>
                    <button type="button" data-dialog-close class="text-[#706f6c] hover:text-[#1b1b18] text-xl leading-none">&times;</button>
                </div>
                <form action="{{ route('familias.update', $familia) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="form_context" value="{{ $familiaEditContext }}">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="sm:col-span-2">
                            <label for="nomeResponsavel{{ $familia->id }}" class="block text-sm font-medium mb-1">Nome do responsável</label>
                            <input type="text" id="nomeResponsavel{{ $familia->id }}" name="nome_responsavel" value="{{ old('form_context') === $familiaEditContext ? old('nome_responsavel') : $familia->nome_responsavel }}" required
                                   class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
                        </div>
                        <div>
                            <label for="cpfResponsavel{{ $familia->id }}" class="block text-sm font-medium mb-1">CPF</label>
                            <input type="text" id="cpfResponsavel{{ $familia->id }}" name="cpf" value="{{ old('form_context') === $familiaEditContext ? old('cpf') : $familia->cpf }}" placeholder="000.000.000-00" required
                                   class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
                        </div>
                        <div>
                            <label for="membrosFamilia{{ $familia->id }}" class="block text-sm font-medium mb-1">Nº de membros</label>
                            <input type="number" id="membrosFamilia{{ $familia->id }}" name="num_membros" min="1" value="{{ old('form_context') === $familiaEditContext ? old('num_membros') : $familia->num_membros }}" required
                                   class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
                        </div>
                        <div>
                            <label for="telefoneFamilia{{ $familia->id }}" class="block text-sm font-medium mb-1">Telefone</label>
                            <input type="tel" id="telefoneFamilia{{ $familia->id }}" name="telefone" value="{{ old('form_context') === $familiaEditContext ? old('telefone') : $familia->telefone }}" placeholder="(00) 00000-0000"
                                   class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
                        </div>
                        <div>
                            <label for="bairroFamilia{{ $familia->id }}" class="block text-sm font-medium mb-1">Bairro</label>
                            <input type="text" id="bairroFamilia{{ $familia->id }}" name="bairro" value="{{ old('form_context') === $familiaEditContext ? old('bairro') : $familia->bairro }}"
                                   class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
                        </div>
                        <div class="sm:col-span-3">
                            <label for="enderecoFamilia{{ $familia->id }}" class="block text-sm font-medium mb-1">Endereço completo</label>
                            <textarea id="enderecoFamilia{{ $familia->id }}" name="endereco" rows="2"
                                      class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">{{ old('form_context') === $familiaEditContext ? old('endereco') : $familia->endereco }}</textarea>
                        </div>
                        <label class="sm:col-span-3 flex items-center gap-2 text-sm">
                            <input type="hidden" name="ativo" value="0">
                            <input type="checkbox" name="ativo" value="1" class="rounded-sm border-[#e3e3e0]" @checked($familiaAtiva)>
                            Família ativa
                        </label>
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
