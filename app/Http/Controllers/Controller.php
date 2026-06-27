<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Database\Eloquent\Model;

abstract class Controller
{
    /**
     * Empresa (EC) do usuário autenticado, quando aplicável.
     */
    protected function empresaAtual(): ?Empresa
    {
        return auth()->user()?->empresa;
    }

    protected function empresaIdAtual(): ?int
    {
        return auth()->user()?->empresa_id;
    }

    /**
     * Garante que o registro pertence à firma do usuário autenticado.
     */
    protected function autorizarEmpresa(Model $model): void
    {
        abort_unless((int) $model->getAttribute('empresa_id') === (int) $this->empresaIdAtual(), 403);
    }
}
