<?php

namespace App\Providers;

use App\Services\Payments\ConfrapixGateway;
use App\Services\Payments\Contracts\PaymentGateway;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(PaymentGateway::class, function ($app) {
            return new ConfrapixGateway($app['config']->get('services.confrapix', []));
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
