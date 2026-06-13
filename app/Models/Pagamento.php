<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pagamento extends Model
{
    use HasFactory;

    public const STATUS_PENDENTE = 'pendente';

    public const STATUS_PAGO = 'pago';

    public const STATUS_EXPIRADO = 'expirado';

    public const STATUS_CANCELADO = 'cancelado';

    protected $table = 'pagamentos';

    protected $fillable = [
        'distribuicao_id',
        'referencia',
        'charge_id',
        'metodo',
        'status',
        'valor_centavos',
        'pagador_nome',
        'pagador_cpf',
        'pagador_email',
        'pagador_telefone',
        'pix_copia_cola',
        'pix_qr_code_base64',
        'expira_em',
        'payload_gateway',
    ];

    protected function casts(): array
    {
        return [
            'valor_centavos' => 'integer',
            'expira_em' => 'datetime',
            'payload_gateway' => 'array',
        ];
    }

    public function getValorReaisAttribute(): float
    {
        return $this->valor_centavos / 100;
    }

    /**
     * @return BelongsTo<Distribuicao, $this>
     */
    public function distribuicao(): BelongsTo
    {
        return $this->belongsTo(Distribuicao::class);
    }
}
