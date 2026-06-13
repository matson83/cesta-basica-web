<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Distribuicao extends Model
{
    use HasFactory;

    protected $table = 'distribuicoes';

    public const STATUS_PENDENTE = 'pendente';

    public const STATUS_PAGA = 'paga';

    public const STATUS_ENTREGUE = 'entregue';

    public const STATUS_CANCELADA = 'cancelada';

    protected $fillable = [
        'familia_id',
        'cesta_id',
        'data_entrega',
        'responsavel',
        'status',
        'observacoes',
    ];

    protected function casts(): array
    {
        return [
            'data_entrega' => 'date',
        ];
    }

    /**
     * @return BelongsTo<Familia, $this>
     */
    public function familia(): BelongsTo
    {
        return $this->belongsTo(Familia::class);
    }

    /**
     * @return BelongsTo<Cesta, $this>
     */
    public function cesta(): BelongsTo
    {
        return $this->belongsTo(Cesta::class);
    }

    /**
     * @return HasOne<Pagamento, $this>
     */
    public function pagamento(): HasOne
    {
        return $this->hasOne(Pagamento::class)->latestOfMany();
    }
}
