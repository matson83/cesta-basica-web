<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Produto extends Model
{
    use BelongsToEmpresa, HasFactory;

    protected $table = 'produtos';

    protected $fillable = [
        'empresa_id',
        'nome',
        'categoria',
        'unidade',
        'estoque',
        'quantidade_por_cesta',
        'preco',
        'ativo',
    ];

    protected function casts(): array
    {
        return [
            'estoque' => 'integer',
            'quantidade_por_cesta' => 'integer',
            'preco' => 'decimal:2',
            'ativo' => 'boolean',
        ];
    }

    /**
     * @return BelongsToMany<Cesta, $this>
     */
    public function cestas(): BelongsToMany
    {
        return $this->belongsToMany(Cesta::class, 'cesta_produto')
            ->withPivot('quantidade')
            ->withTimestamps();
    }
}
