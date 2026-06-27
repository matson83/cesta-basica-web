<?php

namespace App\Services\Payments;

use App\Models\Empresa;
use App\Models\Pagamento;
use App\Services\Payments\Contracts\PaymentGateway;

/**
 * Resolve a instância do gateway de pagamento usando as credenciais (token
 * Confrapix) da firma (EC) correspondente, com fallback para a configuração
 * global em config/services.confrapix.
 */
class PaymentGatewayFactory
{
    public function forEmpresa(?Empresa $empresa): PaymentGateway
    {
        $config = $empresa instanceof Empresa
            ? $empresa->confrapixConfig()
            : (array) config('services.confrapix', []);

        return new ConfrapixGateway($config);
    }

    public function forPagamento(Pagamento $pagamento): PaymentGateway
    {
        return $this->forEmpresa($pagamento->loadMissing('empresa')->empresa);
    }
}
