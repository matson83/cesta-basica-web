<?php

namespace App\Services\Payments\Contracts;

use App\Services\Payments\Data\PixChargeResult;

interface PaymentGateway
{
    /**
     * Cria uma cobrança PIX no gateway.
     *
     * @param  array{amount_in_cents:int, description?:string, reference?:string, payer?:array{name?:string, cpf?:string, email?:string, phone?:string}}  $payload
     */
    public function createPixCharge(array $payload): PixChargeResult;

    /**
     * Consulta o status de uma cobrança previamente criada.
     */
    public function getCharge(string $chargeId): PixChargeResult;
}
