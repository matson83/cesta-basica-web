@php($impulsionamento = $impulsionamento ?? null)
@php($selecionadas = collect(old('empresas', $selecionadas ?? []))->map(fn ($id) => (int) $id))
@php($imagensAtuais = old('imagens', $impulsionamento?->imagensValidas() ?? []))
@php($imagensAtuais = filled($imagensAtuais) ? $imagensAtuais : [''])

<div class="space-y-5">
    <div>
        <label for="titulo" class="block text-sm font-medium mb-1">Título</label>
        <input id="titulo" name="titulo" value="{{ old('titulo', $impulsionamento?->titulo) }}" required maxlength="255"
               placeholder="Ex.: Promoção da semana"
               class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
    </div>

    <div>
        <label for="mensagem" class="block text-sm font-medium mb-1">Mensagem</label>
        <textarea id="mensagem" name="mensagem" rows="5" required maxlength="4000"
                  placeholder="Escreva o texto que será enviado pelo WhatsApp..."
                  class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">{{ old('mensagem', $impulsionamento?->mensagem) }}</textarea>
        <p class="text-xs text-[#706f6c] mt-1">As imagens serão incluídas como links no final da mensagem (o WhatsApp não anexa arquivos por link).</p>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Imagens (URLs)</label>
        <div id="imagens-lista" class="space-y-2">
            @foreach ($imagensAtuais as $url)
                <div class="flex gap-2" data-imagem-item>
                    <input type="url" name="imagens[]" value="{{ $url }}" placeholder="https://exemplo.com/imagem.jpg"
                           class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">
                    <button type="button" data-remover-imagem
                            class="shrink-0 px-3 py-2 text-sm border border-[#e3e3e0] rounded-sm text-[#706f6c] hover:border-[#f53003] hover:text-[#f53003] transition-colors">
                        Remover
                    </button>
                </div>
            @endforeach
        </div>
        <button type="button" id="adicionar-imagem"
                class="mt-2 inline-flex items-center gap-1 text-sm text-[#1b1b18] hover:underline">
            + Adicionar imagem
        </button>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Firmas destinatárias</label>
        <p class="text-xs text-[#706f6c] mb-3">Selecione quem receberá o impulsionamento. O destinatário é o telefone cadastrado da firma.</p>

        @if ($empresas->isEmpty())
            <p class="text-sm text-[#706f6c]">Nenhuma firma cadastrada ainda.</p>
        @else
            <div class="border border-[#e3e3e0] rounded-md divide-y divide-[#e3e3e0] max-h-72 overflow-y-auto">
                @foreach ($empresas as $empresa)
                    <label class="flex items-center justify-between gap-3 px-3 py-2 hover:bg-[#FDFDFC] cursor-pointer">
                        <span class="flex items-center gap-2 min-w-0">
                            <input type="checkbox" name="empresas[]" value="{{ $empresa->id }}"
                                   @checked($selecionadas->contains($empresa->id))
                                   class="rounded-sm border-[#e3e3e0]">
                            <span class="truncate">{{ $empresa->nome_fantasia }}</span>
                        </span>
                        <span class="shrink-0 text-xs {{ $empresa->telefone ? 'text-[#706f6c]' : 'text-[#f53003]' }}">
                            {{ $empresa->telefone ?: 'sem telefone' }}
                        </span>
                    </label>
                @endforeach
            </div>
        @endif
    </div>
</div>

<script>
    (function () {
        const lista = document.getElementById('imagens-lista');
        const adicionar = document.getElementById('adicionar-imagem');
        if (!lista || !adicionar) return;

        const novaLinha = () => {
            const item = document.createElement('div');
            item.className = 'flex gap-2';
            item.setAttribute('data-imagem-item', '');
            item.innerHTML =
                '<input type="url" name="imagens[]" placeholder="https://exemplo.com/imagem.jpg" class="w-full text-sm border border-[#e3e3e0] rounded-sm px-3 py-2 focus:outline-none focus:border-[#1b1b18]">' +
                '<button type="button" data-remover-imagem class="shrink-0 px-3 py-2 text-sm border border-[#e3e3e0] rounded-sm text-[#706f6c] hover:border-[#f53003] hover:text-[#f53003] transition-colors">Remover</button>';
            lista.appendChild(item);
        };

        adicionar.addEventListener('click', novaLinha);

        lista.addEventListener('click', function (event) {
            if (!event.target.matches('[data-remover-imagem]')) return;
            const itens = lista.querySelectorAll('[data-imagem-item]');
            if (itens.length <= 1) {
                event.target.closest('[data-imagem-item]').querySelector('input').value = '';
                return;
            }
            event.target.closest('[data-imagem-item]').remove();
        });
    })();
</script>
