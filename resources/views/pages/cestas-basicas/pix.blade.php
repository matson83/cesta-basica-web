@extends('layouts.app')

@section('title', 'Pagamento PIX')

@section('content')
    <div class="mb-8 pb-6 border-b border-[#e3e3e0]">
        <h1 class="text-xl font-semibold">Pagamento PIX</h1>
        <p class="text-[#706f6c] text-sm">Escaneie o QR Code ou copie o código para pagar</p>
    </div>

    <div class="bg-white rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] p-4 sm:p-6 flex flex-col items-center gap-4">
        <div class="flex flex-wrap items-center justify-center gap-3 text-sm">
            <span class="text-[#706f6c]">Valor:</span>
            <strong>R$ {{ number_format($pagamento->valor_reais, 2, ',', '.') }}</strong>
            <span id="pixStatus" @class([
                'px-2 py-0.5 rounded-sm text-xs font-medium',
                'bg-emerald-50 text-emerald-700' => $pagamento->status === \App\Models\Pagamento::STATUS_PAGO,
                'bg-amber-50 text-amber-700' => $pagamento->status === \App\Models\Pagamento::STATUS_PENDENTE,
                'bg-red-50 text-[#f53003]' => in_array($pagamento->status, [\App\Models\Pagamento::STATUS_EXPIRADO, \App\Models\Pagamento::STATUS_CANCELADO]),
            ])>{{ ucfirst($pagamento->status) }}</span>
        </div>

        <p id="pixAviso" class="text-sm text-[#706f6c] {{ $pagamento->status === \App\Models\Pagamento::STATUS_PENDENTE ? '' : 'hidden' }}">
            Aguardando confirmação do pagamento...
        </p>
        <p id="pixSucesso" class="text-sm font-medium text-emerald-700 {{ $pagamento->status === \App\Models\Pagamento::STATUS_PAGO ? '' : 'hidden' }}">
            Pagamento confirmado! Obrigado.
        </p>

        <div class="bg-white p-3 sm:p-4 rounded-md border border-[#e3e3e0] max-w-full overflow-x-auto">
            @if ($pagamento->pix_qr_code_base64)
                @php
                    $img = $pagamento->pix_qr_code_base64;
                    $src = \Illuminate\Support\Str::startsWith($img, ['data:', 'http://', 'https://'])
                        ? $img
                        : 'data:image/png;base64,'.$img;
                @endphp
                <img src="{{ $src }}" alt="QR Code PIX" width="200" height="200">
            @elseif ($pagamento->pix_copia_cola)
                <div id="qrcode" class="flex items-center justify-center" style="width:200px;height:200px;"></div>
            @else
                <p class="text-sm text-[#706f6c] w-[200px] text-center">QR Code indisponível</p>
            @endif
        </div>

        @if ($pagamento->pix_copia_cola)
            <div class="w-full max-w-md text-center">
                <p class="text-sm text-[#706f6c] mb-1">PIX copia e cola:</p>
                <textarea id="pixCode" readonly rows="3"
                          class="w-full font-mono text-xs border border-[#e3e3e0] rounded-sm px-3 py-2 resize-none">{{ $pagamento->pix_copia_cola }}</textarea>
                <button type="button" id="copyPix" aria-label="Copiar código PIX"
                        class="mt-3 w-full sm:w-auto px-4 py-2 sm:py-1.5 bg-[#1b1b18] text-white rounded-sm text-sm hover:bg-black transition-colors">Copiar código</button>
            </div>
        @else
            <p class="text-sm text-[#706f6c] text-center max-w-md">
                A cobrança foi registrada (referência <span class="font-mono">{{ $pagamento->referencia }}</span>),
                mas o gateway não retornou um código PIX. Verifique a configuração do token e dos endpoints do Confrapix.
            </p>
        @endif
    </div>

    @push('modals')
        <script>
            (function () {
                const alvo = document.getElementById('qrcode');
                const codigo = @json($pagamento->pix_copia_cola);
                if (!alvo || !codigo) return;

                function render() {
                    new QRCode(alvo, { text: codigo, width: 200, height: 200, correctLevel: QRCode.CorrectLevel.M });
                }

                if (window.QRCode) {
                    render();
                } else {
                    const s = document.createElement('script');
                    s.src = 'https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js';
                    s.onload = render;
                    s.onerror = () => { alvo.innerHTML = '<p class="text-sm text-[#706f6c] text-center">Use o copia e cola abaixo.</p>'; };
                    document.head.appendChild(s);
                }
            })();

            (function () {
                const btn = document.getElementById('copyPix');
                const code = document.getElementById('pixCode');
                if (btn && code) {
                    btn.addEventListener('click', async () => {
                        try {
                            await navigator.clipboard.writeText(code.value);
                            btn.textContent = 'Copiado!';
                            setTimeout(() => (btn.textContent = 'Copiar código'), 2000);
                        } catch (e) {
                            code.select();
                            document.execCommand('copy');
                        }
                    });
                }
            })();

            (function () {
                const statusUrl = @json(route('pagamentos.status', $pagamento));
                const jaFinalizado = @json($pagamento->status !== \App\Models\Pagamento::STATUS_PENDENTE);
                if (jaFinalizado) return;

                const badge = document.getElementById('pixStatus');
                const aviso = document.getElementById('pixAviso');
                const sucesso = document.getElementById('pixSucesso');

                const rotulos = { pago: 'Pago', pendente: 'Pendente', expirado: 'Expirado', cancelado: 'Cancelado' };

                async function checar() {
                    try {
                        const resp = await fetch(statusUrl, { headers: { 'Accept': 'application/json' } });
                        if (!resp.ok) return;
                        const data = await resp.json();

                        if (badge) badge.textContent = rotulos[data.status] || data.status;

                        if (data.finalizado) {
                            clearInterval(timer);
                            if (data.pago) {
                                window.location.href = data.redirect || @json(route('pagamentos.sucesso', $pagamento));
                                return;
                            }
                            aviso?.classList.add('hidden');
                            badge?.classList.remove('bg-amber-50', 'text-amber-700');
                            badge?.classList.add('bg-red-50', 'text-[#f53003]');
                        }
                    } catch (e) {
                        // silencia erros transitórios de rede durante o polling
                    }
                }

                const timer = setInterval(checar, 5000);
                checar();
            })();
        </script>
    @endpush
@endsection
