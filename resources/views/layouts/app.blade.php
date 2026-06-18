<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Sistema de gestão de cestas básicas">

    <title>@yield('title', 'Dashboard') — Cesta Básica</title>

    @fonts

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#FDFDFC] text-[#1b1b18] antialiased min-h-screen flex flex-col">
    @include('partials.navbar')

    <main class="flex-1 w-full max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        @if (session('status'))
            <div class="mb-6 rounded-sm border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 rounded-sm border border-red-200 bg-red-50 px-4 py-3 text-sm text-[#f53003]">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-sm border border-red-200 bg-red-50 px-4 py-3 text-sm text-[#f53003]">
                <p class="font-medium mb-1">Verifique os campos do formulário:</p>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    @include('partials.footer')

    @stack('modals')
</body>
</html>
