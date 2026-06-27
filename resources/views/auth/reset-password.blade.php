@extends('layouts.guest')

@section('title', 'Definir senha')

@section('content')
    <h1 class="text-xl font-semibold mb-1">Definir nova senha</h1>
    <p class="text-[#706f6c] text-sm mb-6">Escolha uma senha para acessar sua conta.</p>

    @if ($errors->any())
        <div class="mb-5 rounded-sm border border-red-200 bg-red-50 px-4 py-3 text-sm text-[#f53003]">
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.store') }}" class="flex flex-col gap-4">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div>
            <label for="email" class="block text-sm font-medium mb-1">E-mail</label>
            <input id="email" name="email" type="email" value="{{ old('email', $email) }}" required readonly
                   class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 bg-[#FDFDFC] text-[#706f6c] focus:outline-none">
        </div>
        <div>
            <label for="password" class="block text-sm font-medium mb-1">Nova senha</label>
            <input id="password" name="password" type="password" required autofocus
                   class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
        </div>
        <div>
            <label for="password_confirmation" class="block text-sm font-medium mb-1">Confirmar senha</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required
                   class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
        </div>

        <button type="submit" class="mt-2 w-full px-4 py-2 bg-[#1b1b18] text-white rounded-sm text-sm font-medium hover:bg-black transition-colors">
            Salvar senha
        </button>
    </form>
@endsection
