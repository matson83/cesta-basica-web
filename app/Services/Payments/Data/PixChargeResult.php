<?php

namespace App\Services\Payments\Data;

/**
 * Representa o resultado da criação de uma cobrança PIX, normalizado a partir
 * da resposta do gateway para que o restante da aplicação não dependa do
 * formato específico do provedor.
 */
class PixChargeResult
{
    /**
     * @param  array<string, mixed>  $raw  Resposta original do gateway.
     */
    public function __construct(
        public readonly string $id,
        public readonly string $status,
        public readonly ?string $qrCode,
        public readonly ?string $qrCodeImage,
        public readonly ?int $amountInCents,
        public readonly ?string $expiresAt,
        public readonly array $raw = [],
    ) {
    }
}
