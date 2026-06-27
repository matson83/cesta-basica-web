<?php

namespace App\Providers;

use App\Services\Payments\Contracts\PaymentGateway;
use App\Services\Payments\PaymentGatewayFactory;
use App\Services\WhatsApp\Contracts\WhatsAppGateway;
use App\Services\WhatsApp\EvolutionApiGateway;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(PaymentGatewayFactory::class);

        // Binding de fallback (sem firma) usando a configuração global.
        $this->app->bind(PaymentGateway::class, function ($app) {
            return $app->make(PaymentGatewayFactory::class)->forEmpresa(null);
        });

        // Gateway de WhatsApp (Evolution API) para o envio dos impulsionamentos.
        $this->app->singleton(WhatsAppGateway::class, function () {
            $config = (array) config('services.evolution');

            return new EvolutionApiGateway(
                (string) ($config['base_url'] ?? ''),
                (string) ($config['api_key'] ?? ''),
                (string) ($config['instance'] ?? ''),
                (int) ($config['timeout'] ?? 30),
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
