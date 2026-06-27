@extends('layouts.app')

@section('title', 'Firma cadastrada')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] p-6 sm:p-8 text-center">
            <div class="mx-auto mb-5 flex items-center justify-center w-16 h-16 rounded-full bg-emerald-50">
                <svg class="w-9 h-9 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M20 6 9 17l-5-5" />
                </svg>
            </div>

            <h1 class="text-2xl font-semibold mb-1">Firma cadastrada com sucesso!</h1>
            @if ($emailBoasVindasEnviado)
                <p class="text-[#706f6c] text-sm mb-6">
                    A firma <span class="font-medium text-[#1b1b18]">{{ $empresa->nome_fantasia }}</span> foi criada e um e-mail de boas-vindas
                    foi enviado para <span class="font-medium text-[#1b1b18]">{{ $empresa->email }}</span> definir a senha de acesso.
                </p>
            @else
                <p class="text-[#706f6c] text-sm mb-6">
                    A firma <span class="font-medium text-[#1b1b18]">{{ $empresa->nome_fantasia }}</span> foi criada, mas houve falha no envio
                    do e-mail para <span class="font-medium text-[#1b1b18]">{{ $empresa->email }}</span>.
                </p>
                <div class="rounded-md border border-red-200 bg-red-50 text-left p-3 mb-6">
                    <p class="text-sm text-red-700">
                        Revise as credenciais SMTP no <code>.env</code>. Enquanto isso, use o link abaixo para a firma definir a senha.
                    </p>
                </div>
            @endif

            <div class="rounded-md border border-amber-200 bg-amber-50 text-left p-4 mb-6">
                <p class="text-sm font-semibold text-amber-800 mb-1">Link de definição de senha</p>
                <p class="text-xs text-amber-700 mb-3">
                    Caso o e-mail não chegue, copie o link abaixo e envie para a firma. Ele expira conforme a política de senhas.
                </p>
                <div class="flex flex-col sm:flex-row gap-2">
                    <input id="link-senha" type="text" readonly value="{{ $linkDefinirSenha }}"
                           class="w-full text-xs font-mono border border-amber-200 rounded-sm px-3 py-2 bg-white focus:outline-none focus:border-amber-400 select-all">
                    <button type="button"
                            onclick="(function(b){const i=document.getElementById('link-senha');i.select();navigator.clipboard&&navigator.clipboard.writeText(i.value);b.textContent='Copiado!';setTimeout(()=>b.textContent='Copiar',1500);})(this)"
                            class="shrink-0 inline-flex justify-center px-4 py-2 text-sm bg-amber-600 text-white rounded-sm hover:bg-amber-700 transition-colors">
                        Copiar
                    </button>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 sm:justify-center">
                <a href="{{ route('gestor.empresas.show', $empresa) }}"
                   class="inline-flex justify-center px-4 py-2 text-sm bg-[#1b1b18] text-white rounded-sm hover:bg-black transition-colors">
                    Ver firma
                </a>
                <a href="{{ route('gestor.empresas.create') }}"
                   class="inline-flex justify-center px-4 py-2 text-sm border border-[#e3e3e0] rounded-sm hover:border-[#1b1b18] transition-colors">
                    Cadastrar outra
                </a>
                <a href="{{ route('gestor.empresas.index') }}"
                   class="inline-flex justify-center px-4 py-2 text-sm border border-[#e3e3e0] rounded-sm hover:border-[#1b1b18] transition-colors">
                    Voltar para a lista
                </a>
            </div>
        </div>
    </div>
@endsection
