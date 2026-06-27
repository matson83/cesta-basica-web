@extends('layouts.app')

@section('title', 'Detalhes da Firma')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8 pb-6 border-b border-[#e3e3e0]">
        <div>
            <h1 class="text-xl font-semibold mb-1">{{ $empresa->nome_fantasia }}</h1>
            <p class="text-[#706f6c] text-sm">{{ $empresa->razao_social ?? 'Empresa conveniada' }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('gestor.empresas.edit', $empresa) }}" class="inline-flex justify-center px-4 py-2 sm:py-1.5 text-sm border border-[#e3e3e0] rounded-sm hover:border-[#1b1b18] transition-colors">Editar</a>
            <a href="{{ route('gestor.empresas.index') }}" class="inline-flex justify-center px-4 py-2 sm:py-1.5 text-sm border border-[#e3e3e0] rounded-sm hover:border-[#1b1b18] transition-colors">Voltar</a>
        </div>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        @foreach ([
            ['label' => 'Produtos', 'value' => $empresa->produtos_count],
            ['label' => 'Cestas', 'value' => $empresa->cestas_count],
            ['label' => 'Distribuições', 'value' => $empresa->distribuicoes_count],
            ['label' => 'Acessos', 'value' => $empresa->users_count],
        ] as $stat)
            <div class="bg-white rounded-lg p-4 shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)]">
                <p class="text-[#706f6c] text-xs mb-1">{{ $stat['label'] }}</p>
                <p class="text-xl font-semibold">{{ $stat['value'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] p-5">
            <h2 class="text-sm font-semibold mb-4">Dados cadastrais</h2>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between gap-4">
                    <dt class="text-[#706f6c]">{{ $empresa->documentoLabel() }}</dt>
                    <dd class="text-right">{{ $empresa->documento ?? 'Não informado' }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-[#706f6c]">E-mail de acesso</dt>
                    <dd class="text-right break-all">{{ $empresa->email ?? 'Não informado' }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-[#706f6c]">Telefone</dt>
                    <dd class="text-right">{{ $empresa->telefone ?? 'Não informado' }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-[#706f6c]">Localização</dt>
                    <dd class="text-right">{{ collect([$empresa->bairro, $empresa->cidade, $empresa->uf])->filter()->implode(' - ') ?: 'Não informada' }}</dd>
                </div>
                @if ($empresa->endereco)
                    <div class="flex justify-between gap-4">
                        <dt class="text-[#706f6c]">Endereço</dt>
                        <dd class="text-right">{{ $empresa->endereco }}</dd>
                    </div>
                @endif
                <div class="flex justify-between gap-4">
                    <dt class="text-[#706f6c]">Status</dt>
                    <dd class="text-right">
                        <span @class([
                            'text-xs font-medium px-2 py-0.5 rounded-sm',
                            'bg-emerald-50 text-emerald-700' => $empresa->ativo,
                            'bg-[#dbdbd7] text-[#706f6c]' => ! $empresa->ativo,
                        ])>{{ $empresa->ativo ? 'Ativa' : 'Inativa' }}</span>
                    </dd>
                </div>
            </dl>
        </div>

        <div class="bg-white rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] p-5">
            <h2 class="text-sm font-semibold mb-4">Pagamento (Confrapix)</h2>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between gap-4">
                    <dt class="text-[#706f6c]">Token</dt>
                    <dd class="text-right">
                        @if ($empresa->temTokenConfrapix())
                            <span class="text-xs font-medium px-2 py-0.5 rounded-sm bg-emerald-50 text-emerald-700">Configurado</span>
                        @else
                            <span class="text-xs font-medium px-2 py-0.5 rounded-sm bg-red-50 text-[#f53003]">Pendente</span>
                        @endif
                    </dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-[#706f6c]">Base URL</dt>
                    <dd class="text-right break-all">{{ $empresa->confrapix_base_url ?? config('services.confrapix.base_url') }}</dd>
                </div>
            </dl>
            <p class="text-xs text-[#706f6c] mt-4">Por segurança, o token não é exibido. Para substituí-lo, edite a firma e informe um novo valor.</p>
        </div>
    </div>
@endsection
