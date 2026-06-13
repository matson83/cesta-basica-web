<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Familia extends Model
{
    use HasFactory;

    protected $table = 'familias';

    protected $fillable = [
        'nome_responsavel',
        'cpf',
        'num_membros',
        'telefone',
        'bairro',
        'endereco',
        'ativo',
    ];

    protected function casts(): array
    {
        return [
            'num_membros' => 'integer',
            'ativo' => 'boolean',
        ];
    }

    /**
     * @return HasMany<Distribuicao, $this>
     */
    public function distribuicoes(): HasMany
    {
        return $this->hasMany(Distribuicao::class);
    }
}
