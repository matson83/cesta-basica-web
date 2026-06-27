@extends('layouts.guest')

@section('title', 'Recuperar senha')

@section('content')
    <h1 class="text-xl font-semibold mb-1">Recuperar senha</h1>
    <p class="text-[#706f6c] text-sm mb-6">Informe seu e-mail e enviaremos um link para definir uma nova senha.</p>

    @if ($errors->any())
        <div class="mb-5 rounded-sm border border-red-200 bg-red-50 px-4 py-3 text-sm text-[#f53003]">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-4">
        @csrf
        <div>
            <label for="email" class="block text-sm font-medium mb-1">E-mail</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                   class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
        </div>

        <button type="submit" class="mt-2 w-full px-4 py-2 bg-[#1b1b18] text-white rounded-sm text-sm font-medium hover:bg-black transition-colors">
            Enviar link
        </button>
    </form>

    <div class="mt-5 text-center">
        <a href="{{ route('login') }}" class="text-sm text-[#706f6c] hover:text-[#1b1b18] hover:underline">Voltar para o login</a>
    </div>
@endsection
