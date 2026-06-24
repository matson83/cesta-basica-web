<?php

use App\Http\Controllers\CestaBasicaController;
use App\Http\Controllers\DistribuicaoController;
use App\Http\Controllers\FamiliaController;
use App\Http\Controllers\PagamentoController;
use App\Http\Controllers\ProdutoController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('pages.dashboard'))->name('dashboard');

Route::resource('produtos', ProdutoController::class)
    ->only(['index', 'store', 'update', 'destroy']);

Route::resource('familias', FamiliaController::class)
    ->only(['index', 'store', 'update', 'destroy']);

Route::resource('distribuicoes', DistribuicaoController::class)
    ->only(['index', 'store', 'update', 'destroy'])
    ->parameters(['distribuicoes' => 'distribuicao']);

Route::resource('cestas-basicas', CestaBasicaController::class);

/* Pagamentos (gateway Confrapix) */
Route::controller(PagamentoController::class)->group(function () {
    Route::post('distribuicoes/{distribuicao}/pagar', 'pagar')->name('pagamentos.pagar');
    Route::get('pagamentos/{pagamento}/pix', 'pix')->name('pagamentos.pix');
    Route::get('pagamentos/{pagamento}/comprovante', 'comprovante')->name('pagamentos.comprovante');
    Route::get('pagamentos/{pagamento}/status', 'status')->name('pagamentos.status');
});

/* Preview routes for cestas-basicas views (frontend-only, temporary) */
Route::prefix('preview')->name('preview.')->group(function () {
    Route::view('dashboard', 'pages.cestas-basicas.dashboard')->name('dashboard');
    Route::view('cart', 'pages.cestas-basicas.cart')->name('cart');
    Route::view('history', 'pages.cestas-basicas.history')->name('history');
});
