<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cesta extends Model
{
    use BelongsToEmpresa, HasFactory;

    protected $table = 'cestas';

    protected $fillable = [
        'empresa_id',
        'nome',
        'descricao',
        'categoria',
        'ativo',
    ];

    protected function casts(): array
    {
        return [
            'ativo' => 'boolean',
        ];
    }

    /**
     * @return BelongsToMany<Produto, $this>
     */
    public function produtos(): BelongsToMany
    {
        return $this->belongsToMany(Produto::class, 'cesta_produto')
            ->withPivot('quantidade')
            ->withTimestamps();
    }

    /**
     * @return HasMany<Distribuicao, $this>
     */
    public function distribuicoes(): HasMany
    {
        return $this->hasMany(Distribuicao::class);
    }

    /**
     * Valor total da cesta com base nos produtos e suas quantidades.
     */
    protected function valorTotal(): Attribute
    {
        return Attribute::get(fn () => $this->produtos->sum(
            fn (Produto $produto) => $produto->preco * $produto->pivot->quantidade
        ));
    }

    /**
     * Quantidade total de itens da cesta.
     */
    protected function totalItens(): Attribute
    {
        return Attribute::get(fn () => (int) $this->produtos->sum('pivot.quantidade'));
    }
}
