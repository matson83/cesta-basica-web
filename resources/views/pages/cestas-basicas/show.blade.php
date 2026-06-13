@extends('layouts.app')

@section('title', 'Detalhes da Cesta')

@section('content')
    <div class="mb-8 pb-6 border-b border-[#e3e3e0]">
        <h1 class="text-xl font-semibold">Detalhes da Cesta</h1>
        <p class="text-[#706f6c] text-sm">Visualização completa da composição da cesta</p>
    </div>

    <div class="bg-white rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] p-6">
        <div class="flex items-start justify-between gap-6 mb-6">
            <div>
                <h2 class="text-lg font-semibold">{{ $cesta->nome }}</h2>
                <p class="text-sm text-[#706f6c] mt-1">{{ $cesta->descricao }}</p>
            </div>
            <div class="flex items-start gap-6">
                <div class="text-right">
                    <p class="text-sm text-[#706f6c]">Itens</p>
                    <p class="text-xl font-semibold">{{ $cesta->total_itens }}</p>
                </div>
                <a href="{{ route('cestas-basicas.edit', $cesta) }}" class="px-3 py-1.5 text-sm border border-[#e3e3e0] rounded-sm hover:border-[#1b1b18] transition-colors">Editar</a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm mb-4">
                <caption class="sr-only">Produtos incluídos na cesta</caption>
                <thead>
                    <tr class="border-b border-[#e3e3e0] text-left text-[#706f6c]">
                        <th scope="col" class="pb-3 font-medium">Produto</th>
                        <th scope="col" class="pb-3 font-medium">Quantidade</th>
                        <th scope="col" class="pb-3 font-medium text-right">Valor unit.</th>
                        <th scope="col" class="pb-3 font-medium text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e3e3e0]">
                    @forelse ($cesta->produtos as $p)
                        <tr class="hover:bg-[#FDFDFC]">
                            <td class="py-3">{{ $p->nome }}</td>
                            <td class="py-3">{{ $p->pivot->quantidade }}</td>
                            <td class="py-3 text-right">R$ {{ number_format($p->preco, 2, ',', '.') }}</td>
                            <td class="py-3 text-right">R$ {{ number_format($p->pivot->quantidade * $p->preco, 2, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-6 text-center text-[#706f6c]">Esta cesta ainda não possui produtos.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex justify-end items-center gap-4 pt-4 border-t border-[#e3e3e0]">
            <div class="text-right">
                <p class="text-sm text-[#706f6c]">Valor total</p>
                <p class="text-xl font-semibold">R$ {{ number_format($cesta->valor_total, 2, ',', '.') }}</p>
            </div>
        </div>
    </div>
@endsection
