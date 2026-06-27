<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\CestaBasicaController;
use App\Http\Controllers\DistribuicaoController;
use App\Http\Controllers\FamiliaController;
use App\Http\Controllers\Gestor\DashboardController as GestorDashboardController;
use App\Http\Controllers\Gestor\EmpresaController;
use App\Http\Controllers\Gestor\ImpulsionamentoController;
use App\Http\Controllers\PagamentoController;
use App\Http\Controllers\ProdutoController;
use Illuminate\Support\Facades\Route;

/* Autenticação (visitantes) */
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

/* Raiz: redireciona conforme o papel do usuário */
Route::get('/', function () {
    return redirect(AuthenticatedSessionController::destinoPorPapel(auth()->user()));
})->middleware('auth')->name('home');

/* Área do Gestor (LA) */
Route::middleware(['auth', 'role:gestor'])
    ->prefix('gestor')
    ->name('gestor.')
    ->group(function () {
        Route::get('/', [GestorDashboardController::class, 'index'])->name('dashboard');
        Route::get('empresas/{empresa}/criada', [EmpresaController::class, 'criada'])->name('empresas.criada');
        Route::resource('empresas', EmpresaController::class);

        Route::post('impulsionamentos/{impulsionamento}/disparar', [ImpulsionamentoController::class, 'disparar'])
            ->name('impulsionamentos.disparar');
        Route::post('impulsionamentos/{impulsionamento}/firmas/{empresa}/enviar', [ImpulsionamentoController::class, 'enviarFirma'])
            ->name('impulsionamentos.enviar');
        Route::post('impulsionamentos/{impulsionamento}/firmas/{empresa}/enviado', [ImpulsionamentoController::class, 'marcarEnviado'])
            ->name('impulsionamentos.enviado');
        Route::resource('impulsionamentos', ImpulsionamentoController::class);
    });

/* Área da Firma (EC) */
Route::middleware(['auth', 'role:empresa'])->group(function () {
    Route::view('dashboard', 'pages.dashboard')->name('dashboard');

    Route::resource('produtos', ProdutoController::class)
        ->only(['index', 'store', 'update', 'destroy']);

    Route::resource('familias', FamiliaController::class)
        ->only(['index', 'store', 'update', 'destroy']);

    Route::resource('distribuicoes', DistribuicaoController::class)
        ->only(['index', 'store', 'update', 'destroy'])
        ->parameters(['distribuicoes' => 'distribuicao']);

    Route::resource('cestas-basicas', CestaBasicaController::class);

    /* Pagamentos (gateway Confrapix por firma) */
    Route::controller(PagamentoController::class)->group(function () {
        Route::post('distribuicoes/{distribuicao}/pagar', 'pagar')->name('pagamentos.pagar');
        Route::get('pagamentos/{pagamento}/pix', 'pix')->name('pagamentos.pix');
        Route::get('pagamentos/{pagamento}/sucesso', 'sucesso')->name('pagamentos.sucesso');
        Route::get('pagamentos/{pagamento}/comprovante', 'comprovante')->name('pagamentos.comprovante');
        Route::get('pagamentos/{pagamento}/status', 'status')->name('pagamentos.status');
    });
});
