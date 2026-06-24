@extends('layouts.comprovante')

@section('title', 'Comprovante de pagamento')

@php
    $distribuicao = $pagamento->distribuicao;
    $familia = $distribuicao?->familia;
    $cesta = $distribuicao?->cesta;
    $dataPagamento = $pagamento->dataPagamento();
@endphp

@section('content')
    <div class="no-print max-w-3xl mx-auto px-4 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <a href="{{ route('distribuicoes.index') }}"
           class="inline-flex justify-center px-4 py-2 text-sm border border-[#e3e3e0] rounded-sm hover:border-[#1b1b18] transition-colors">
            Voltar para distribuições
        </a>
        <button type="button" onclick="window.print()"
                class="inline-flex justify-center px-4 py-2 text-sm bg-[#1b1b18] text-white rounded-sm hover:bg-black transition-colors">
            Imprimir comprovante
        </button>
    </div>

    <main class="max-w-3xl mx-auto px-4 pb-10 print:px-0 print:pb-0">
        <article class="comprovante bg-white rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] print:shadow-none print:rounded-none overflow-hidden">
            <header class="px-6 py-5 border-b border-[#e3e3e0] flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="flex items-center justify-center w-8 h-8 rounded-md bg-[#1b1b18] text-white text-sm font-semibold">CB</span>
                        <span class="font-semibold">Cesta Básica</span>
                    </div>
                    <h1 class="text-lg font-semibold">Comprovante de pagamento</h1>
                    <p class="text-sm text-[#706f6c] mt-1">Documento emitido eletronicamente</p>
                </div>
                <div class="text-left sm:text-right">
                    <span class="inline-flex px-2.5 py-1 rounded-sm text-xs font-medium bg-emerald-50 text-emerald-700">
                        Pagamento confirmado
                    </span>
                    <p class="text-sm text-[#706f6c] mt-2">Nº {{ $pagamento->numeroComprovante() }}</p>
                </div>
            </header>

            <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5 border-b border-[#e3e3e0]">
                <div>
                    <h2 class="text-xs font-semibold uppercase tracking-wide text-[#706f6c] mb-2">Pagamento</h2>
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between gap-4">
                            <dt class="text-[#706f6c]">Valor pago</dt>
                            <dd class="font-semibold">R$ {{ number_format($pagamento->valor_reais, 2, ',', '.') }}</dd>
                        </div>
                        <div class="flex justify-between gap-4">
                            <dt class="text-[#706f6c]">Método</dt>
                            <dd class="font-medium uppercase">{{ $pagamento->metodo }}</dd>
                        </div>
                        <div class="flex justify-between gap-4">
                            <dt class="text-[#706f6c]">Data do pagamento</dt>
                            <dd>{{ $dataPagamento?->format('d/m/Y H:i') ?? 'Não informada' }}</dd>
                        </div>
                        <div class="flex justify-between gap-4">
                            <dt class="text-[#706f6c]">Referência</dt>
                            <dd class="font-mono text-xs break-all text-right">{{ $pagamento->referencia }}</dd>
                        </div>
                        @if ($pagamento->identificadorTransacao())
                            <div class="flex justify-between gap-4">
                                <dt class="text-[#706f6c]">ID transação</dt>
                                <dd class="font-mono text-xs break-all text-right">{{ $pagamento->identificadorTransacao() }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>

                <div>
                    <h2 class="text-xs font-semibold uppercase tracking-wide text-[#706f6c] mb-2">Pagador</h2>
                    <dl class="space-y-2 text-sm">
                        <div>
                            <dt class="text-[#706f6c]">Nome</dt>
                            <dd class="font-medium">{{ $pagamento->pagador_nome ?? $familia?->nome_responsavel ?? 'Não informado' }}</dd>
                        </div>
                        @if ($pagamento->pagador_cpf ?? $familia?->cpf)
                            <div>
                                <dt class="text-[#706f6c]">CPF</dt>
                                <dd>{{ $pagamento->pagador_cpf ?? $familia?->cpf }}</dd>
                            </div>
                        @endif
                        @if ($pagamento->pagador_telefone ?? $familia?->telefone)
                            <div>
                                <dt class="text-[#706f6c]">Telefone</dt>
                                <dd>{{ $pagamento->pagador_telefone ?? $familia?->telefone }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>

            <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5 border-b border-[#e3e3e0]">
                <div>
                    <h2 class="text-xs font-semibold uppercase tracking-wide text-[#706f6c] mb-2">Distribuição</h2>
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between gap-4">
                            <dt class="text-[#706f6c]">Código</dt>
                            <dd>#{{ $distribuicao?->id ?? '—' }}</dd>
                        </div>
                        <div class="flex justify-between gap-4">
                            <dt class="text-[#706f6c]">Data de entrega</dt>
                            <dd>{{ $distribuicao?->data_entrega?->format('d/m/Y') ?? 'Não informada' }}</dd>
                        </div>
                        <div class="flex justify-between gap-4">
                            <dt class="text-[#706f6c]">Responsável</dt>
                            <dd>{{ $distribuicao?->responsavel ?? 'Não informado' }}</dd>
                        </div>
                    </dl>
                </div>

                <div>
                    <h2 class="text-xs font-semibold uppercase tracking-wide text-[#706f6c] mb-2">Família / Beneficiário</h2>
                    <dl class="space-y-2 text-sm">
                        <div>
                            <dt class="text-[#706f6c]">Nome</dt>
                            <dd class="font-medium">{{ $familia?->nome_responsavel ?? 'Não informado' }}</dd>
                        </div>
                        @if ($familia?->bairro)
                            <div>
                                <dt class="text-[#706f6c]">Bairro</dt>
                                <dd>{{ $familia->bairro }}</dd>
                            </div>
                        @endif
                        @if ($familia?->endereco)
                            <div>
                                <dt class="text-[#706f6c]">Endereço</dt>
                                <dd>{{ $familia->endereco }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>

            @if ($cesta)
                <div class="px-6 py-5 border-b border-[#e3e3e0]">
                    <h2 class="text-xs font-semibold uppercase tracking-wide text-[#706f6c] mb-3">Cesta adquirida</h2>
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4 text-sm">
                        <div>
                            <p class="font-semibold">{{ $cesta->nome }}</p>
                            @if ($cesta->descricao)
                                <p class="text-[#706f6c] mt-1">{{ $cesta->descricao }}</p>
                            @endif
                        </div>
                        <p class="text-[#706f6c]">{{ $cesta->total_itens }} itens</p>
                    </div>

                    @if ($cesta->produtos->isNotEmpty())
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-[#e3e3e0] text-left text-[#706f6c]">
                                        <th class="pb-2 font-medium">Produto</th>
                                        <th class="pb-2 font-medium text-right">Qtd.</th>
                                        <th class="pb-2 font-medium text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#e3e3e0]">
                                    @foreach ($cesta->produtos as $produto)
                                        <tr>
                                            <td class="py-2">{{ $produto->nome }}</td>
                                            <td class="py-2 text-right">{{ $produto->pivot->quantidade }}</td>
                                            <td class="py-2 text-right">
                                                R$ {{ number_format($produto->pivot->quantidade * $produto->preco, 2, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            @endif

            <footer class="px-6 py-5 bg-[#FDFDFC] text-sm text-[#706f6c]">
                <p>Este comprovante attesta a confirmação do pagamento PIX registrado no sistema.</p>
                <p class="mt-2">Emitido em {{ now()->format('d/m/Y H:i') }}.</p>
            </footer>
        </article>
    </main>
@endsection

@push('head')
    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background: #fff;
            }

            .comprovante {
                box-shadow: none !important;
            }
        }
    </style>
@endpush
