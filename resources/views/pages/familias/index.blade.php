@extends('layouts.app')

@section('title', 'Famílias')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8 pb-6 border-b border-[#e3e3e0]">
        <div>
            <h1 class="text-xl font-semibold mb-1">Famílias</h1>
            <p class="text-[#706f6c] text-sm">Beneficiários cadastrados no programa</p>
        </div>
        <button type="button" data-dialog-open="modalFamilia"
                class="inline-flex items-center gap-1.5 px-4 py-1.5 bg-[#1b1b18] text-white rounded-sm text-sm font-medium hover:bg-black transition-colors">
            Nova família
        </button>
    </div>

    <div class="bg-white rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] p-5">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-5">
            <div class="md:col-span-2">
                <input type="search" placeholder="Buscar por nome ou CPF..."
                       class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
            </div>
            <select class="text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
                <option selected>Todos os status</option>
                <option>Ativo</option>
                <option>Inativo</option>
            </select>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
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
                    @foreach ([
                        ['nome' => 'Maria Silva', 'cpf' => '123.456.789-00', 'membros' => 4, 'bairro' => 'Centro', 'tel' => '(11) 98765-4321', 'ativo' => true],
                        ['nome' => 'João Santos', 'cpf' => '987.654.321-00', 'membros' => 3, 'bairro' => 'Jardim Primavera', 'tel' => '(11) 91234-5678', 'ativo' => true],
                        ['nome' => 'Ana Oliveira', 'cpf' => '456.789.123-00', 'membros' => 5, 'bairro' => 'Vila Nova', 'tel' => '(11) 99876-5432', 'ativo' => true],
                        ['nome' => 'Carlos Pereira', 'cpf' => '321.654.987-00', 'membros' => 2, 'bairro' => 'Parque das Águas', 'tel' => '(11) 97654-3210', 'ativo' => false],
                        ['nome' => 'Fernanda Costa', 'cpf' => '654.321.789-00', 'membros' => 6, 'bairro' => 'Centro', 'tel' => '(11) 96543-2109', 'ativo' => true],
                    ] as $familia)
                        <tr class="hover:bg-[#FDFDFC]">
                            <td class="py-3 font-medium">{{ $familia['nome'] }}</td>
                            <td class="py-3 text-[#706f6c]">{{ $familia['cpf'] }}</td>
                            <td class="py-3">{{ $familia['membros'] }}</td>
                            <td class="py-3 text-[#706f6c]">{{ $familia['bairro'] }}</td>
                            <td class="py-3 text-[#706f6c]">{{ $familia['tel'] }}</td>
                            <td class="py-3">
                                <span @class([
                                    'text-xs font-medium px-2 py-0.5 rounded-sm',
                                    'bg-emerald-50 text-emerald-700' => $familia['ativo'],
                                    'bg-[#dbdbd7] text-[#706f6c]' => ! $familia['ativo'],
                                ])>{{ $familia['ativo'] ? 'Ativo' : 'Inativo' }}</span>
                            </td>
                            <td class="py-3 text-right">
                                <button class="px-2 py-1 text-xs border border-[#e3e3e0] rounded-sm hover:border-[#1b1b18] transition-colors">Editar</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="flex justify-end gap-1 mt-5 pt-4 border-t border-[#e3e3e0]">
            <span class="px-3 py-1 text-xs border border-[#e3e3e0] rounded-sm text-[#706f6c]">Anterior</span>
            <span class="px-3 py-1 text-xs bg-[#1b1b18] text-white rounded-sm">1</span>
            <a href="#" class="px-3 py-1 text-xs border border-[#e3e3e0] rounded-sm hover:border-[#1b1b18] transition-colors">2</a>
            <a href="#" class="px-3 py-1 text-xs border border-[#e3e3e0] rounded-sm hover:border-[#1b1b18] transition-colors">Próximo</a>
        </div>
    </div>
@endsection

@push('modals')
    <dialog id="modalFamilia" data-form-dialog
            class="backdrop:bg-black/40 bg-transparent p-0 max-w-lg w-full rounded-lg">
        <div class="bg-white rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] p-6">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-base font-semibold">Cadastrar família</h2>
                <button type="button" data-dialog-close class="text-[#706f6c] hover:text-[#1b1b18] text-xl leading-none">&times;</button>
            </div>
            <form class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="sm:col-span-2">
                        <label for="nomeResponsavel" class="block text-sm font-medium mb-1">Nome do responsável</label>
                        <input type="text" id="nomeResponsavel" required
                               class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
                    </div>
                    <div>
                        <label for="cpfResponsavel" class="block text-sm font-medium mb-1">CPF</label>
                        <input type="text" id="cpfResponsavel" placeholder="000.000.000-00" required
                               class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
                    </div>
                    <div>
                        <label for="membrosFamilia" class="block text-sm font-medium mb-1">Nº de membros</label>
                        <input type="number" id="membrosFamilia" min="1" value="1" required
                               class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
                    </div>
                    <div>
                        <label for="telefoneFamilia" class="block text-sm font-medium mb-1">Telefone</label>
                        <input type="tel" id="telefoneFamilia" placeholder="(00) 00000-0000"
                               class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
                    </div>
                    <div>
                        <label for="bairroFamilia" class="block text-sm font-medium mb-1">Bairro</label>
                        <input type="text" id="bairroFamilia" required
                               class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
                    </div>
                    <div class="sm:col-span-3">
                        <label for="enderecoFamilia" class="block text-sm font-medium mb-1">Endereço completo</label>
                        <textarea id="enderecoFamilia" rows="2" required
                                  class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]"></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" data-dialog-close class="px-4 py-1.5 text-sm border border-[#e3e3e0] rounded-sm hover:border-[#1b1b18] transition-colors">Cancelar</button>
                    <button type="submit" class="px-4 py-1.5 text-sm bg-[#1b1b18] text-white rounded-sm hover:bg-black transition-colors">Salvar</button>
                </div>
            </form>
        </div>
    </dialog>
@endpush
