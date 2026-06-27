@extends('layouts.app')

@section('title', 'Impulsionamento')

@php($gestor = $impulsionamento->gestor)
@php($imagens = $impulsionamento->imagensValidas())

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8 pb-6 border-b border-[#e3e3e0]">
        <div>
            <h1 class="text-xl font-semibold mb-1">{{ $impulsionamento->titulo }}</h1>
            <p class="text-[#706f6c] text-sm">Criado em {{ $impulsionamento->created_at?->format('d/m/Y H:i') }}</p>
        </div>
        <div class="flex gap-2">
            @if ($gatewayConfigurado)
                <form action="{{ route('gestor.impulsionamentos.disparar', $impulsionamento) }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex justify-center px-4 py-2 sm:py-1.5 text-sm bg-emerald-600 text-white rounded-sm hover:bg-emerald-700 transition-colors">
                        Disparar para pendentes
                    </button>
                </form>
            @endif
            <a href="{{ route('gestor.impulsionamentos.edit', $impulsionamento) }}" class="inline-flex justify-center px-4 py-2 sm:py-1.5 text-sm border border-[#e3e3e0] rounded-sm hover:border-[#1b1b18] transition-colors">Editar</a>
            <a href="{{ route('gestor.impulsionamentos.index') }}" class="inline-flex justify-center px-4 py-2 sm:py-1.5 text-sm border border-[#e3e3e0] rounded-sm hover:border-[#1b1b18] transition-colors">Voltar</a>
        </div>
    </div>

    @if ($gatewayConfigurado)
        <div class="mb-6 rounded-sm border px-4 py-3 text-sm {{ ($conexaoEvolution['conectada'] ?? false) ? 'border-emerald-200 bg-emerald-50 text-emerald-800' : 'border-red-200 bg-red-50 text-red-800' }}">
            @if ($conexaoEvolution['conectada'] ?? false)
                WhatsApp conectado na instância <strong>{{ $instanciaEvolution }}</strong> (estado: {{ $conexaoEvolution['state'] }}).
                A mensagem sai desse número e chega no telefone de cada firma listada abaixo.
            @else
                WhatsApp <strong>desconectado</strong> (estado: {{ $conexaoEvolution['state'] ?? 'indisponível' }}).
                Abra <a href="{{ rtrim(env('EVOLUTION_SERVER_URL', 'http://localhost:8080'), '/') }}/manager" target="_blank" rel="noopener" class="underline">Evolution Manager</a>,
                reconecte a instância escaneando o QR Code e tente novamente.
            @endif
        </div>
    @endif

    @unless ($gatewayConfigurado)
        <div class="mb-6 rounded-sm border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
            Envio automático desativado: o gateway de WhatsApp (Evolution API) não está configurado.
            Defina <code>EVOLUTION_API_URL</code>, <code>EVOLUTION_API_KEY</code> e <code>EVOLUTION_INSTANCE</code> no <code>.env</code>.
            Enquanto isso, use o botão <strong>Abrir WhatsApp</strong> de cada firma para enviar manualmente.
        </div>
    @endunless

    @unless ($gestor?->temTelefone())
        <div class="mb-6 rounded-sm border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
            O gestor remetente está sem telefone cadastrado. O envio funciona mesmo assim, mas o número de origem será o WhatsApp em que você estiver logado.
        </div>
    @endunless

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] p-5">
            <h2 class="text-sm font-semibold mb-4">Pré-visualização da mensagem</h2>

            <div class="rounded-md border border-[#e3e3e0] bg-[#FDFDFC] p-4 text-sm">
                <p class="font-semibold mb-2">{{ $impulsionamento->titulo }}</p>
                <p class="whitespace-pre-line text-[#1b1b18]">{{ $impulsionamento->mensagem }}</p>

                @if ($imagens !== [])
                    <div class="mt-4 grid grid-cols-2 sm:grid-cols-3 gap-2">
                        @foreach ($imagens as $url)
                            <a href="{{ $url }}" target="_blank" rel="noopener" class="block">
                                <img src="{{ $url }}" alt="Imagem do impulsionamento" loading="lazy"
                                     class="w-full h-24 object-cover rounded-sm border border-[#e3e3e0]">
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <dl class="mt-4 space-y-2 text-sm">
                <div class="flex justify-between gap-4">
                    <dt class="text-[#706f6c]">Remetente</dt>
                    <dd class="text-right">{{ $gestor?->nome ?? $gestor?->user?->name ?? 'Gestor' }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-[#706f6c]">Instância Evolution</dt>
                    <dd class="text-right">{{ $instanciaEvolution ?: 'Não configurada' }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-[#706f6c]">Telefone do remetente</dt>
                    <dd class="text-right">{{ $gestor?->telefone ? '+'.(\App\Support\WhatsApp::normalizar($gestor->telefone) ?? $gestor->telefone) : 'Não informado' }}</dd>
                </div>
            </dl>
        </div>

        <div class="bg-white rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] p-5">
            <h2 class="text-sm font-semibold mb-4">Firmas destinatárias ({{ $impulsionamento->empresas->count() }})</h2>

            <div class="space-y-2">
                @forelse ($impulsionamento->empresas as $empresa)
                    @php($link = $impulsionamento->linkWhatsApp($empresa))
                    @php($enviado = $empresa->pivot->enviado_em)
                    @php($numeroFormatado = \App\Support\WhatsApp::normalizar($empresa->telefone))
                    @php($wa = $whatsappPorEmpresa[$empresa->id] ?? null)
                    <div class="rounded-md border border-[#e3e3e0] p-3">
                        <div class="flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <p class="font-medium truncate">{{ $empresa->nome_fantasia }}</p>
                                <p class="text-xs {{ $empresa->telefone ? 'text-[#706f6c]' : 'text-[#f53003]' }}">
                                    @if ($numeroFormatado)
                                        {{ $empresa->telefone }} → +{{ $numeroFormatado }}
                                        @if ($wa && $wa['exists'] && $wa['numero_envio'] !== $wa['numero_informado'])
                                            <span class="text-amber-700"> (WhatsApp: +{{ $wa['numero_envio'] }})</span>
                                        @endif
                                    @else
                                        Sem telefone cadastrado
                                    @endif
                                </p>
                                @if ($wa)
                                    <p class="text-xs mt-1 {{ $wa['exists'] ? 'text-emerald-700' : ($wa['inconclusivo'] ?? false ? 'text-amber-700' : 'text-[#f53003]') }}">
                                        @if ($wa['exists'])
                                            WhatsApp encontrado{{ $wa['numero_envio'] && $wa['numero_envio'] !== $wa['numero_informado'] ? ' (envio: +'.$wa['numero_envio'].')' : '' }}
                                        @elseif ($wa['inconclusivo'] ?? false)
                                            Verificação inconclusiva — o envio será tentado com variações do número
                                        @else
                                            WhatsApp não confirmado neste número pela Evolution
                                        @endif
                                    </p>
                                @endif
                            </div>
                            @if ($enviado)
                                <span class="shrink-0 text-xs font-medium px-2 py-0.5 rounded-sm bg-emerald-50 text-emerald-700">
                                    Enviado {{ \Illuminate\Support\Carbon::parse($enviado)->format('d/m H:i') }}
                                </span>
                            @else
                                <span class="shrink-0 text-xs font-medium px-2 py-0.5 rounded-sm bg-[#dbdbd7] text-[#706f6c]">Pendente</span>
                            @endif
                        </div>

                        <div class="flex flex-wrap gap-2 mt-3">
                            @if ($gatewayConfigurado && $empresa->telefone)
                                <form action="{{ route('gestor.impulsionamentos.enviar', [$impulsionamento, $empresa]) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs bg-emerald-600 text-white rounded-sm hover:bg-emerald-700 transition-colors">
                                        {{ $enviado ? 'Reenviar agora' : 'Enviar agora' }}
                                    </button>
                                </form>
                            @endif

                            @if ($link)
                                <a href="{{ $link }}" target="_blank" rel="noopener"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs border border-emerald-600 text-emerald-700 rounded-sm hover:bg-emerald-50 transition-colors">
                                    WhatsApp manual
                                </a>
                            @else
                                <span class="inline-flex items-center px-3 py-1.5 text-xs border border-[#e3e3e0] rounded-sm text-[#706f6c]">
                                    Telefone inválido
                                </span>
                            @endif

                            <form action="{{ route('gestor.impulsionamentos.enviado', [$impulsionamento, $empresa]) }}" method="POST">
                                @csrf
                                <button type="submit"
                                        class="inline-flex items-center px-3 py-1.5 text-xs border border-[#e3e3e0] rounded-sm hover:border-[#1b1b18] transition-colors">
                                    {{ $enviado ? 'Marcar reenvio' : 'Marcar como enviado' }}
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-[#706f6c]">Nenhuma firma vinculada a este impulsionamento.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
