@extends('layouts.guest')

@section('title', 'Entrar')

@section('content')
    <h1 class="text-xl font-semibold mb-1">Entrar</h1>
    <p class="text-[#706f6c] text-sm mb-6">Acesse com seu e-mail e senha</p>

    @if ($errors->any())
        <div class="mb-5 rounded-sm border border-red-200 bg-red-50 px-4 py-3 text-sm text-[#f53003]">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-4">
        @csrf
        <div>
            <label for="email" class="block text-sm font-medium mb-1">E-mail</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                   class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
        </div>
        <div>
            <label for="password" class="block text-sm font-medium mb-1">Senha</label>
            <input id="password" name="password" type="password" required
                   class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
        </div>
        <label class="flex items-center gap-2 text-sm text-[#706f6c]">
            <input type="checkbox" name="remember" class="rounded-sm border-[#e3e3e0]">
            Manter conectado
        </label>

        <button type="submit" class="mt-2 w-full px-4 py-2 bg-[#1b1b18] text-white rounded-sm text-sm font-medium hover:bg-black transition-colors">
            Entrar
        </button>
    </form>

    <div class="mt-5 text-center">
        <a href="{{ route('password.request') }}" class="text-sm text-[#f53003] hover:underline">Esqueci minha senha</a>
    </div>
@endsection
