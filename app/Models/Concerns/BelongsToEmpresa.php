<?php

namespace App\Models\Concerns;

use App\Models\Empresa;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Marca um model como pertencente a uma firma (EC) e oferece atalhos de
 * escopo por tenant.
 */
trait BelongsToEmpresa
{
    /**
     * @return BelongsTo<Empresa, $this>
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeForEmpresa(Builder $query, int|Empresa|null $empresa): Builder
    {
        $empresaId = $empresa instanceof Empresa ? $empresa->id : $empresa;

        return $query->where($this->getTable().'.empresa_id', $empresaId);
    }
}
