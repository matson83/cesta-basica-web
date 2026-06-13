<header class="border-b border-[#e3e3e0] bg-white shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_1px_2px_0px_rgba(0,0,0,0.06)]">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-14">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 font-semibold text-[#1b1b18]">
                <span class="flex items-center justify-center w-8 h-8 rounded-md bg-[#1b1b18] text-white text-sm">CB</span>
                Cesta Básica
            </a>

            <nav class="hidden md:flex items-center gap-1">
                @foreach ([
                    ['route' => 'dashboard', 'label' => 'Dashboard', 'match' => 'dashboard'],
                    ['route' => 'produtos.index', 'label' => 'Produtos', 'match' => 'produtos.*'],
                    ['route' => 'familias.index', 'label' => 'Famílias', 'match' => 'familias.*'],
                    ['route' => 'distribuicoes.index', 'label' => 'Distribuições', 'match' => 'distribuicoes.*'],
                ] as $item)
                    <a href="{{ route($item['route']) }}"
                       @class([
                           'px-3 py-1.5 rounded-sm text-sm transition-colors',
                           'bg-[#1b1b18] text-white font-medium' => request()->routeIs($item['match']),
                           'text-[#706f6c] hover:text-[#1b1b18] hover:bg-[#FDFDFC]' => ! request()->routeIs($item['match']),
                       ])>
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>

            <div class="hidden md:flex items-center gap-2 text-sm text-[#706f6c]">
                <span class="w-7 h-7 rounded-full bg-[#dbdbd7] flex items-center justify-center text-xs font-medium">A</span>
                Administrador
            </div>

            <details class="md:hidden relative">
                <summary class="list-none cursor-pointer p-2 rounded-sm border border-[#e3e3e0] text-[#706f6c]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </summary>
                <nav class="absolute right-0 top-full mt-2 w-48 bg-white border border-[#e3e3e0] rounded-md shadow-lg py-1 z-50">
                    @foreach ([
                        ['route' => 'dashboard', 'label' => 'Dashboard', 'match' => 'dashboard'],
                        ['route' => 'produtos.index', 'label' => 'Produtos', 'match' => 'produtos.*'],
                        ['route' => 'familias.index', 'label' => 'Famílias', 'match' => 'familias.*'],
                        ['route' => 'distribuicoes.index', 'label' => 'Distribuições', 'match' => 'distribuicoes.*'],
                    ] as $item)
                        <a href="{{ route($item['route']) }}"
                           @class([
                               'block px-4 py-2 text-sm',
                               'bg-[#FDFDFC] font-medium text-[#1b1b18]' => request()->routeIs($item['match']),
                               'text-[#706f6c] hover:bg-[#FDFDFC]' => ! request()->routeIs($item['match']),
                           ])>
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </nav>
            </details>
        </div>
    </div>
</header>
