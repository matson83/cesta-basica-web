@extends('layouts.app')

@section('title', 'Impulsionamentos')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8 pb-6 border-b border-[#e3e3e0]">
        <div>
            <h1 class="text-xl font-semibold mb-1">Impulsionamentos</h1>
            <p class="text-[#706f6c] text-sm">Campanhas enviadas às firmas conveniadas pelo WhatsApp</p>
        </div>
        <a href="{{ route('gestor.impulsionamentos.create') }}"
           class="inline-flex items-center justify-center gap-1.5 px-4 py-2 sm:py-1.5 bg-[#1b1b18] text-white rounded-sm text-sm font-medium hover:bg-black transition-colors">
            Novo impulsionamento
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] p-4 sm:p-5">
        <div class="app-table-wrap">
            <table class="app-table text-sm" style="--table-min-width: 40rem">
                <thead>
                    <tr class="border-b border-[#e3e3e0] text-left text-[#706f6c]">
                        <th class="pb-3 font-medium">Título</th>
                        <th class="pb-3 font-medium">Firmas</th>
                        <th class="pb-3 font-medium">Enviados</th>
                        <th class="pb-3 font-medium">Criado em</th>
                        <th class="pb-3 font-medium text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e3e3e0]">
                    @forelse ($impulsionamentos as $impulsionamento)
                        <tr class="hover:bg-[#FDFDFC]">
                            <td class="py-3 font-medium">{{ $impulsionamento->titulo }}</td>
                            <td class="py-3 text-[#706f6c]">{{ $impulsionamento->empresas_count }}</td>
                            <td class="py-3 text-[#706f6c]">{{ $impulsionamento->enviados_count }} / {{ $impulsionamento->empresas_count }}</td>
                            <td class="py-3 text-[#706f6c]">{{ $impulsionamento->created_at?->format('d/m/Y H:i') }}</td>
                            <td class="py-3 text-right">
                                <div class="app-table-actions">
                                    <a href="{{ route('gestor.impulsionamentos.show', $impulsionamento) }}"
                                       class="px-2 py-1 text-xs border border-[#e3e3e0] rounded-sm hover:border-[#1b1b18] transition-colors">Ver</a>
                                    <a href="{{ route('gestor.impulsionamentos.edit', $impulsionamento) }}"
                                       class="px-2 py-1 text-xs border border-[#e3e3e0] rounded-sm hover:border-[#1b1b18] transition-colors">Editar</a>
                                    <form id="form-remover-impulsionamento-{{ $impulsionamento->id }}" action="{{ route('gestor.impulsionamentos.destroy', $impulsionamento) }}" method="POST" class="contents">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                                data-confirm-delete
                                                data-confirm-form="form-remover-impulsionamento-{{ $impulsionamento->id }}"
                                                data-confirm-title="Excluir impulsionamento"
                                                data-confirm-message="Deseja realmente excluir &quot;{{ $impulsionamento->titulo }}&quot;?"
                                                class="px-2 py-1 text-xs border border-[#e3e3e0] rounded-sm hover:border-[#1b1b18] transition-colors">
                                            Remover
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-[#706f6c]">Nenhum impulsionamento criado ainda.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-5 pt-4 border-t border-[#e3e3e0] overflow-x-auto">
            {{ $impulsionamentos->links() }}
        </div>
    </div>
@endsection
