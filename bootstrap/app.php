<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Middleware\EnsureUserRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => EnsureUserRole::class,
        ]);

        $middleware->redirectGuestsTo(fn () => route('login'));

        // Usuários autenticados que acessam rotas de visitante vão ao destino do seu papel
        // (evita o gestor cair em /dashboard, exclusiva da firma, e tomar 403).
        $middleware->redirectUsersTo(
            fn (Request $request) => AuthenticatedSessionController::destinoPorPapel($request->user())
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
