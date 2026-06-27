<?php

namespace App\Http\Controllers\Gestor;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\Pagamento;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'empresas' => Empresa::count(),
            'ativas' => Empresa::where('ativo', true)->count(),
            'pagamentos_pagos' => Pagamento::where('status', Pagamento::STATUS_PAGO)->count(),
        ];

        $empresas = Empresa::query()
            ->withCount('produtos')
            ->latest()
            ->take(5)
            ->get();

        return view('pages.gestor.dashboard', compact('stats', 'empresas'));
    }
}
