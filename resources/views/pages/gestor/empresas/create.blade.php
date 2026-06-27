@extends('layouts.app')

@section('title', 'Nova Firma')

@section('content')
    <div class="mb-8 pb-6 border-b border-[#e3e3e0]">
        <h1 class="text-xl font-semibold">Nova Firma</h1>
        <p class="text-[#706f6c] text-sm">Cadastre uma empresa conveniada e defina seu token de pagamento</p>
    </div>

    <form action="{{ route('gestor.empresas.store') }}" method="POST"
          class="bg-white rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] p-4 sm:p-6">
        @csrf

        @include('pages.gestor.empresas._form-fields', ['empresa' => null])

        <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 border-t border-[#e3e3e0] mt-6 pt-5">
            <a href="{{ route('gestor.empresas.index') }}" class="inline-flex justify-center px-4 py-2 text-sm border border-[#e3e3e0] rounded-sm hover:border-[#1b1b18] transition-colors">Cancelar</a>
            <button type="submit" class="inline-flex justify-center px-4 py-2 text-sm bg-[#1b1b18] text-white rounded-sm hover:bg-black transition-colors">
                Cadastrar e enviar boas-vindas
            </button>
        </div>
    </form>
@endsection
