@props(['produtos', 'cesta' => null])

@php
    $limiteVisivel = 5;
    $totalProdutos = $produtos->count();
@endphp

<div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_18rem] gap-4 items-start">
    <div class="min-w-0">
        @if ($totalProdutos > $limiteVisivel)
            <div class="mb-3 space-y-2">
                <label for="buscaProdutosCesta" class="sr-only">Buscar produto</label>
                <input type="search"
                       id="buscaProdutosCesta"
                       placeholder="Buscar produto..."
                       class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
                <p class="text-xs text-[#706f6c]">
                    Exibindo {{ $limiteVisivel }} de {{ $totalProdutos }} produtos. Use a busca para encontrar os demais.
                </p>
            </div>
        @endif

        <div class="rounded-lg border border-[#e3e3e0] overflow-hidden">
            <div class="app-table-wrap !mx-0 !px-0 !max-w-none">
                <table id="cestaProdutosTable" class="app-table app-table--fluid text-sm cesta-produtos-table">
                    <colgroup>
                        <col>
                        <col style="width: 7rem">
                        <col style="width: 5rem">
                    </colgroup>
                    <thead>
                        <tr class="border-b border-[#e3e3e0] bg-[#FDFDFC] text-left text-[#706f6c]">
                            <th scope="col" class="py-2.5 pl-4 font-medium">Produto</th>
                            <th scope="col" class="py-2.5 font-medium text-right">Preço unit.</th>
                            <th scope="col" class="py-2.5 pr-4 font-medium text-right"><span class="sr-only">Adicionar</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#e3e3e0]">
                        @forelse ($produtos as $index => $p)
                            @php
                                $pivot = $cesta?->produtos->firstWhere('id', $p->id)?->pivot;
                                $produtoQuantidade = max(0, (int) old("product.{$p->id}.qty", $pivot->quantidade ?? 0));
                                $visivelInicial = $index < $limiteVisivel;
                            @endphp
                            <tr class="produto-row hover:bg-[#FDFDFC]"
                                data-search-row="{{ $p->nome }}"
                                data-row-index="{{ $index }}"
                                data-preco="{{ $p->preco }}"
                                @unless($visivelInicial) hidden @endunless>
                                <td class="py-3 pl-4 pr-3 align-middle">
                                    <p class="font-medium">{{ $p->nome }}</p>
                                    <p class="produto-na-sacola mt-1 text-xs text-emerald-700 {{ $produtoQuantidade > 0 ? '' : 'hidden' }}">
                                        {{ $produtoQuantidade }} na sacola
                                    </p>
                                </td>
                                <td class="py-3 align-middle text-right text-[#706f6c] whitespace-nowrap">
                                    R$ {{ number_format($p->preco, 2, ',', '.') }}
                                </td>
                                <td class="py-3 pr-4 align-middle text-right">
                                    <input type="hidden"
                                           name="product[{{ $p->id }}][qty]"
                                           value="{{ $produtoQuantidade }}"
                                           class="produto-qtd">
                                    <button type="button"
                                            class="produto-adicionar inline-flex h-8 w-8 items-center justify-center rounded-sm border border-[#e3e3e0] text-lg leading-none hover:border-[#1b1b18] hover:bg-[#FDFDFC] transition-colors"
                                            aria-label="Adicionar {{ $p->nome }}">
                                        +
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-6 text-center text-[#706f6c]">
                                    Nenhum produto cadastrado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <p class="mt-2 text-xs text-[#706f6c]">Clique em + para adicionar produtos à sacola ao lado.</p>
    </div>

    <aside class="rounded-lg border border-[#e3e3e0] bg-[#FDFDFC] p-4 lg:sticky lg:top-6">
        <div class="flex items-center justify-between gap-2 mb-4">
            <h3 class="text-sm font-semibold">Sacola</h3>
            <span id="cestaSacolaContagem" class="text-xs font-medium px-2 py-0.5 rounded-sm bg-[#dbdbd7] text-[#706f6c]">0 itens</span>
        </div>

        <div id="cestaSacolaVazia" class="py-8 text-center text-sm text-[#706f6c]">
            Nenhum produto adicionado.
        </div>

        <ul id="cestaSacolaItens" class="space-y-3 hidden"></ul>
    </aside>
</div>

@once
    @push('modals')
        <script>
            (function () {
                const LIMITE = {{ $limiteVisivel }};
                const formatMoney = (value) => 'R$ ' + value.toFixed(2).replace('.', ',');
                const searchInput = document.getElementById('buscaProdutosCesta');
                const rows = Array.from(document.querySelectorAll('#cestaProdutosTable .produto-row'));
                const sacolaItens = document.getElementById('cestaSacolaItens');
                const sacolaVazia = document.getElementById('cestaSacolaVazia');
                const sacolaContagem = document.getElementById('cestaSacolaContagem');

                const normalize = (value) => value
                    .toString()
                    .normalize('NFD')
                    .replace(/\p{Diacritic}/gu, '')
                    .toLowerCase();

                function obterQuantidade(row) {
                    return Math.max(0, Number(row.querySelector('.produto-qtd')?.value || 0));
                }

                function definirQuantidade(row, quantidade) {
                    const input = row.querySelector('.produto-qtd');
                    const qtd = Math.max(0, quantidade);

                    if (input) {
                        input.value = qtd;
                    }

                    const indicador = row.querySelector('.produto-na-sacola');
                    if (indicador) {
                        indicador.textContent = `${qtd} na sacola`;
                        indicador.classList.toggle('hidden', qtd === 0);
                    }
                }

                function alterarQuantidade(row, delta) {
                    definirQuantidade(row, obterQuantidade(row) + delta);
                    atualizar();
                }

                function aplicarListagem() {
                    const query = normalize(searchInput?.value || '');

                    rows.forEach((row) => {
                        const nome = normalize(row.dataset.searchRow || '');

                        if (query !== '') {
                            row.hidden = !nome.includes(query);

                            return;
                        }

                        const index = Number(row.dataset.rowIndex || 0);
                        row.hidden = index >= LIMITE;
                    });
                }

                function renderSacola() {
                    const itens = rows.filter((row) => obterQuantidade(row) > 0);
                    let totalItens = 0;
                    let totalValor = 0;

                    sacolaItens.innerHTML = '';

                    itens.forEach((row) => {
                        const preco = Number(row.dataset.preco || 0);
                        const qtd = obterQuantidade(row);
                        const subtotal = qtd * preco;

                        totalItens += qtd;
                        totalValor += subtotal;

                        const item = document.createElement('li');
                        item.className = 'rounded-sm border border-[#e3e3e0] bg-white p-3';
                        item.innerHTML = `
                            <div class="flex items-start justify-between gap-2 mb-3">
                                <p class="text-sm font-medium leading-snug">${row.dataset.searchRow || ''}</p>
                                <button type="button" class="sacola-remover shrink-0 text-xs text-[#f53003] hover:underline">Remover</button>
                            </div>
                            <div class="flex items-center justify-between gap-2">
                                <div class="inline-flex items-center rounded-sm border border-[#e3e3e0] overflow-hidden">
                                    <button type="button" class="sacola-menos px-2 py-1 text-sm hover:bg-[#FDFDFC] transition-colors" aria-label="Diminuir quantidade">−</button>
                                    <span class="sacola-qtd min-w-8 px-2 py-1 text-sm text-center border-x border-[#e3e3e0]">${qtd}</span>
                                    <button type="button" class="sacola-mais px-2 py-1 text-sm hover:bg-[#FDFDFC] transition-colors" aria-label="Aumentar quantidade">+</button>
                                </div>
                                <span class="text-sm font-medium">${formatMoney(subtotal)}</span>
                            </div>
                        `;

                        item.querySelector('.sacola-remover')?.addEventListener('click', () => {
                            definirQuantidade(row, 0);
                            atualizar();
                        });

                        item.querySelector('.sacola-menos')?.addEventListener('click', () => {
                            alterarQuantidade(row, -1);
                        });

                        item.querySelector('.sacola-mais')?.addEventListener('click', () => {
                            alterarQuantidade(row, 1);
                        });

                        sacolaItens.appendChild(item);
                    });

                    const temItens = itens.length > 0;
                    sacolaItens.classList.toggle('hidden', !temItens);
                    sacolaVazia.classList.toggle('hidden', temItens);
                    sacolaContagem.textContent = `${totalItens} ${totalItens === 1 ? 'item' : 'itens'}`;

                    const totalElemento = document.getElementById('totalCesta');
                    if (totalElemento) {
                        totalElemento.textContent = formatMoney(totalValor);
                    }
                }

                function atualizar() {
                    renderSacola();
                    aplicarListagem();
                }

                rows.forEach((row) => {
                    row.querySelector('.produto-adicionar')?.addEventListener('click', () => {
                        alterarQuantidade(row, 1);
                    });
                });

                searchInput?.addEventListener('input', aplicarListagem);

                atualizar();
            })();
        </script>
    @endpush
@endonce
