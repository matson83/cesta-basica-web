<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('pages.dashboard'))->name('dashboard');

Route::prefix('produtos')->name('produtos.')->group(function () {
    Route::get('/', fn () => view('pages.produtos.index'))->name('index');
});

Route::prefix('familias')->name('familias.')->group(function () {
    Route::get('/', fn () => view('pages.familias.index'))->name('index');
});

Route::prefix('distribuicoes')->name('distribuicoes.')->group(function () {
    Route::get('/', fn () => view('pages.distribuicoes.index'))->name('index');
});

/* Preview routes for cestas-basicas views (frontend-only, temporary) */
Route::prefix('preview')->name('preview.')->group(function () {
    Route::view('cestas', 'pages.cestas-basicas.index')->name('cestas');
    Route::view('dashboard', 'pages.cestas-basicas.dashboard')->name('dashboard');
    Route::view('show', 'pages.cestas-basicas.show')->name('show');
    Route::view('cart', 'pages.cestas-basicas.cart')->name('cart');
    Route::view('checkout', 'pages.cestas-basicas.checkout')->name('checkout');
    Route::view('pix', 'pages.cestas-basicas.pix')->name('pix');
    Route::view('history', 'pages.cestas-basicas.history')->name('history');
});
