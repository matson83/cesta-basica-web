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

    public const STATUS_PAGO = 'pago';

    public const STATUS_CANCELADO = 'cancelado';

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
     * @return array<string, string>
     */
    public static function statusOpcoes(): array
    {
        return [
            self::STATUS_PENDENTE => 'Pendente',
            self::STATUS_PAGO => 'Pago',
            self::STATUS_CANCELADO => 'Cancelado',
        ];
    }

    /**
     * @return list<string>
     */
    public static function statusValidos(): array
    {
        return array_keys(self::statusOpcoes());
    }

    public static function normalizeStatus(?string $status): string
    {
        return match ($status) {
            self::STATUS_PAGO, 'paga', 'entregue' => self::STATUS_PAGO,
            self::STATUS_CANCELADO, 'cancelada' => self::STATUS_CANCELADO,
            default => self::STATUS_PENDENTE,
        };
    }

    public function statusLabel(): string
    {
        return self::statusOpcoes()[self::normalizeStatus($this->status)] ?? 'Pendente';
    }

    public function isPago(): bool
    {
        return self::normalizeStatus($this->status) === self::STATUS_PAGO;
    }

    public function isPendente(): bool
    {
        return self::normalizeStatus($this->status) === self::STATUS_PENDENTE;
    }

    public function isCancelado(): bool
    {
        return self::normalizeStatus($this->status) === self::STATUS_CANCELADO;
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
