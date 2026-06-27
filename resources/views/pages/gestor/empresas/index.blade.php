@extends('layouts.app')

@section('title', 'Firmas Conveniadas')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8 pb-6 border-b border-[#e3e3e0]">
        <div>
            <h1 class="text-xl font-semibold mb-1">Firmas Conveniadas</h1>
            <p class="text-[#706f6c] text-sm">Gerencie as empresas (EC) e seus tokens de pagamento</p>
        </div>
        <a href="{{ route('gestor.empresas.create') }}"
           class="inline-flex items-center justify-center gap-1.5 px-4 py-2 sm:py-1.5 bg-[#1b1b18] text-white rounded-sm text-sm font-medium hover:bg-black transition-colors">
            Nova firma
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] p-4 sm:p-5">
        <form method="GET" action="{{ route('gestor.empresas.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-5">
            <div class="md:col-span-3">
                <input type="search" name="busca" value="{{ request('busca') }}" placeholder="Buscar por nome, razão social ou documento..."
                       class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
            </div>
            <div class="flex flex-col sm:flex-row gap-2">
                <button type="submit" class="inline-flex justify-center px-4 py-2 text-sm bg-[#1b1b18] text-white rounded-sm hover:bg-black transition-colors">Filtrar</button>
                <a href="{{ route('gestor.empresas.index') }}" class="inline-flex justify-center px-4 py-2 text-sm border border-[#e3e3e0] rounded-sm hover:border-[#1b1b18] transition-colors">Limpar</a>
            </div>
        </form>

        <div class="app-table-wrap">
            <table class="app-table text-sm" style="--table-min-width: 52rem">
                <thead>
                    <tr class="border-b border-[#e3e3e0] text-left text-[#706f6c]">
                        <th class="pb-3 font-medium">Firma</th>
                        <th class="pb-3 font-medium">Documento</th>
                        <th class="pb-3 font-medium">E-mail</th>
                        <th class="pb-3 font-medium">Token PIX</th>
                        <th class="pb-3 font-medium">Status</th>
                        <th class="pb-3 font-medium text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e3e3e0]">
                    @forelse ($empresas as $empresa)
                        <tr class="hover:bg-[#FDFDFC]">
                            <td class="py-3 font-medium">{{ $empresa->nome_fantasia }}</td>
                            <td class="py-3 text-[#706f6c]">{{ $empresa->documento ? $empresa->documentoLabel() . ': ' . $empresa->documento : '—' }}</td>
                            <td class="py-3 text-[#706f6c]">{{ $empresa->email ?? '—' }}</td>
                            <td class="py-3">
                                @if ($empresa->temTokenConfrapix())
                                    <span class="text-xs font-medium px-2 py-0.5 rounded-sm bg-emerald-50 text-emerald-700">Configurado</span>
                                @else
                                    <span class="text-xs font-medium px-2 py-0.5 rounded-sm bg-red-50 text-[#f53003]">Pendente</span>
                                @endif
                            </td>
                            <td class="py-3">
                                <span @class([
                                    'text-xs font-medium px-2 py-0.5 rounded-sm',
                                    'bg-emerald-50 text-emerald-700' => $empresa->ativo,
                                    'bg-[#dbdbd7] text-[#706f6c]' => ! $empresa->ativo,
                                ])>{{ $empresa->ativo ? 'Ativa' : 'Inativa' }}</span>
                            </td>
                            <td class="py-3 text-right">
                                <div class="app-table-actions">
                                    <a href="{{ route('gestor.empresas.show', $empresa) }}"
                                       class="px-2 py-1 text-xs border border-[#e3e3e0] rounded-sm hover:border-[#1b1b18] transition-colors">Ver</a>
                                    <a href="{{ route('gestor.empresas.edit', $empresa) }}"
                                       class="px-2 py-1 text-xs border border-[#e3e3e0] rounded-sm hover:border-[#1b1b18] transition-colors">Editar</a>
                                    <form id="form-remover-empresa-{{ $empresa->id }}" action="{{ route('gestor.empresas.destroy', $empresa) }}" method="POST" class="contents">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                                data-confirm-delete
                                                data-confirm-form="form-remover-empresa-{{ $empresa->id }}"
                                                data-confirm-title="Excluir firma"
                                                data-confirm-message="Deseja realmente excluir a firma &quot;{{ $empresa->nome_fantasia }}&quot;? Os acessos vinculados também serão removidos."
                                                class="px-2 py-1 text-xs border border-[#e3e3e0] rounded-sm hover:border-[#1b1b18] transition-colors">
                                            Remover
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-[#706f6c]">Nenhuma firma cadastrada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-5 pt-4 border-t border-[#e3e3e0] overflow-x-auto">
            {{ $empresas->links() }}
        </div>
    </div>
@endsection
