@php
    $user = auth()->user();
    $isGestor = $user?->isGestor() ?? false;

    $navItems = $isGestor
        ? [
            ['route' => 'gestor.dashboard', 'label' => 'Painel', 'match' => 'gestor.dashboard'],
            ['route' => 'gestor.empresas.index', 'label' => 'Firmas', 'match' => 'gestor.empresas.*'],
            ['route' => 'gestor.impulsionamentos.index', 'label' => 'Impulsionamentos', 'match' => 'gestor.impulsionamentos.*'],
        ]
        : [
            ['route' => 'dashboard', 'label' => 'Dashboard', 'match' => 'dashboard'],
            ['route' => 'cestas-basicas.index', 'label' => 'Cestas', 'match' => 'cestas-basicas.*'],
            ['route' => 'produtos.index', 'label' => 'Produtos', 'match' => 'produtos.*'],
            ['route' => 'familias.index', 'label' => 'Famílias', 'match' => 'familias.*'],
            ['route' => 'distribuicoes.index', 'label' => 'Distribuições', 'match' => 'distribuicoes.*'],
        ];

    $homeRoute = $isGestor ? 'gestor.dashboard' : 'dashboard';
    $inicial = strtoupper(mb_substr($user?->name ?? 'U', 0, 1));
@endphp

<header class="border-b border-[#e3e3e0] bg-white shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_1px_2px_0px_rgba(0,0,0,0.06)]">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-14">
            <a href="{{ route($homeRoute) }}" class="flex items-center gap-2 font-semibold text-[#1b1b18]">
                <span class="flex items-center justify-center w-8 h-8 rounded-md bg-[#1b1b18] text-white text-sm">CB</span>
                Cesta Básica
            </a>

            <nav class="hidden md:flex items-center gap-1">
                @foreach ($navItems as $item)
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

            <div class="hidden md:flex items-center gap-3 text-sm text-[#706f6c]">
                <span class="flex items-center gap-2">
                    <span class="w-7 h-7 rounded-full bg-[#dbdbd7] flex items-center justify-center text-xs font-medium">{{ $inicial }}</span>
                    <span class="max-w-[12rem] truncate">{{ $user?->name }}</span>
                </span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-[#706f6c] hover:text-[#f53003] transition-colors">Sair</button>
                </form>
            </div>

            <details class="md:hidden relative">
                <summary class="list-none cursor-pointer p-2 rounded-sm border border-[#e3e3e0] text-[#706f6c]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </summary>
                <nav class="absolute right-0 top-full mt-2 w-56 bg-white border border-[#e3e3e0] rounded-md shadow-lg py-1 z-50">
                    <div class="px-4 py-2 text-xs text-[#706f6c] border-b border-[#e3e3e0] truncate">{{ $user?->name }}</div>
                    @foreach ($navItems as $item)
                        <a href="{{ route($item['route']) }}"
                           @class([
                               'block px-4 py-2 text-sm',
                               'bg-[#FDFDFC] font-medium text-[#1b1b18]' => request()->routeIs($item['match']),
                               'text-[#706f6c] hover:bg-[#FDFDFC]' => ! request()->routeIs($item['match']),
                           ])>
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                    <form method="POST" action="{{ route('logout') }}" class="border-t border-[#e3e3e0] mt-1">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-[#706f6c] hover:bg-[#FDFDFC] hover:text-[#f53003]">Sair</button>
                    </form>
                </nav>
            </details>
        </div>
    </div>
</header>
