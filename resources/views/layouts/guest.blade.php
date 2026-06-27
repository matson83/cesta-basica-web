<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Sistema de gestão de vendas via PIX">

    <title>@yield('title', 'Acessar') — Cesta Básica</title>

    @fonts

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#FDFDFC] text-[#1b1b18] antialiased min-h-screen flex flex-col">
    <main class="flex-1 w-full max-w-md mx-auto px-4 py-10 sm:py-16 flex flex-col justify-center">
        <div class="flex items-center justify-center gap-2 mb-8">
            <span class="flex items-center justify-center w-9 h-9 rounded-md bg-[#1b1b18] text-white text-sm font-semibold">CB</span>
            <span class="font-semibold text-lg">Cesta Básica</span>
        </div>

        @if (session('status'))
            <div class="mb-6 rounded-sm border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] p-6 sm:p-8">
            @yield('content')
        </div>
    </main>
</body>
</html>
