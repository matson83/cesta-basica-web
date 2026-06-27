@php($empresa = $empresa ?? null)

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label for="nome_fantasia" class="block text-sm font-medium mb-1">Nome fantasia</label>
        <input id="nome_fantasia" name="nome_fantasia" value="{{ old('nome_fantasia', $empresa?->nome_fantasia) }}" required
               class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
    </div>
    <div>
        <label for="razao_social" class="block text-sm font-medium mb-1">Razão social</label>
        <input id="razao_social" name="razao_social" value="{{ old('razao_social', $empresa?->razao_social) }}"
               class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
    </div>
    <div>
        <label for="documento" class="block text-sm font-medium mb-1">Documento</label>
        <div class="flex gap-2">
            <select id="tipo_documento" name="tipo_documento" data-tipo-documento
                    class="text-sm border border-[#e3e3e0] rounded-sm px-2 py-2 focus:outline-none focus:border-[#1b1b18]">
                @foreach (\App\Models\Empresa::tiposDocumento() as $valor => $rotulo)
                    <option value="{{ $valor }}" @selected(old('tipo_documento', $empresa?->tipo_documento ?? 'cnpj') === $valor)>{{ $rotulo }}</option>
                @endforeach
            </select>
            <input id="documento" name="documento" value="{{ old('documento', $empresa?->documento) }}" data-documento
                   placeholder="00.000.000/0000-00"
                   class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
        </div>
    </div>
    <div>
        <label for="email" class="block text-sm font-medium mb-1">E-mail de acesso</label>
        <input id="email" name="email" type="email" value="{{ old('email', $empresa?->email) }}" required
               class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
        <p class="text-xs text-[#706f6c] mt-1">Será o login da firma e o destino do e-mail de boas-vindas.</p>
    </div>
    <div>
        <label for="telefone" class="block text-sm font-medium mb-1">Telefone</label>
        <input id="telefone" name="telefone" value="{{ old('telefone', $empresa?->telefone) }}" placeholder="(00) 00000-0000"
               class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
    </div>
    <div class="grid grid-cols-3 gap-3">
        <div class="col-span-2">
            <label for="cidade" class="block text-sm font-medium mb-1">Cidade</label>
            <input id="cidade" name="cidade" value="{{ old('cidade', $empresa?->cidade) }}"
                   class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
        </div>
        <div>
            <label for="uf" class="block text-sm font-medium mb-1">UF</label>
            <input id="uf" name="uf" maxlength="2" value="{{ old('uf', $empresa?->uf) }}"
                   class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 uppercase focus:outline-none focus:border-[#1b1b18]">
        </div>
    </div>
    <div>
        <label for="bairro" class="block text-sm font-medium mb-1">Bairro</label>
        <input id="bairro" name="bairro" value="{{ old('bairro', $empresa?->bairro) }}"
               class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
    </div>
    <div class="sm:col-span-2">
        <label for="endereco" class="block text-sm font-medium mb-1">Endereço completo</label>
        <textarea id="endereco" name="endereco" rows="2"
                  class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">{{ old('endereco', $empresa?->endereco) }}</textarea>
    </div>
</div>

<div class="border-t border-[#e3e3e0] mt-5 pt-5">
    <h2 class="text-sm font-semibold mb-1">Pagamento (Confrapix)</h2>
    <p class="text-[#706f6c] text-sm mb-4">Credenciais usadas para gerar as cobranças PIX desta firma.</p>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label for="confrapix_token" class="block text-sm font-medium mb-1">Token Confrapix</label>
            <input id="confrapix_token" name="confrapix_token" value="{{ old('confrapix_token') }}"
                   placeholder="{{ $empresa?->temTokenConfrapix() ? '•••••••• (mantém o atual se vazio)' : 'Cole o token da firma' }}"
                   @if (! $empresa) required @endif
                   class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
            @if ($empresa)
                <p class="text-xs text-[#706f6c] mt-1">Deixe em branco para manter o token atual.</p>
            @endif
        </div>
        <div>
            <label for="confrapix_base_url" class="block text-sm font-medium mb-1">Base URL (opcional)</label>
            <input id="confrapix_base_url" name="confrapix_base_url" value="{{ old('confrapix_base_url', $empresa?->confrapix_base_url) }}"
                   placeholder="https://api.confrapix.com.br"
                   class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
        </div>
    </div>
</div>

<label class="flex items-center gap-2 text-sm mt-5">
    <input type="hidden" name="ativo" value="0">
    <input type="checkbox" name="ativo" value="1" class="rounded-sm border-[#e3e3e0]" @checked(old('ativo', $empresa?->ativo ?? true))>
    Firma ativa
</label>

<script>
    (function () {
        const tipo = document.querySelector('[data-tipo-documento]');
        const documento = document.querySelector('[data-documento]');
        if (!tipo || !documento) return;

        const placeholders = { cpf: '000.000.000-00', cnpj: '00.000.000/0000-00' };
        const aplicar = () => { documento.placeholder = placeholders[tipo.value] ?? ''; };

        tipo.addEventListener('change', aplicar);
        aplicar();
    })();
</script>
