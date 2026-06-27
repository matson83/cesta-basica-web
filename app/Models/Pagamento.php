<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pagamento extends Model
{
    use BelongsToEmpresa, HasFactory;

    public const STATUS_PENDENTE = 'pendente';

    public const STATUS_PAGO = 'pago';

    public const STATUS_EXPIRADO = 'expirado';

    public const STATUS_CANCELADO = 'cancelado';

    protected $table = 'pagamentos';

    protected $fillable = [
        'empresa_id',
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

    public function isPago(): bool
    {
        return $this->status === self::STATUS_PAGO;
    }

    public function numeroComprovante(): string
    {
        return str_pad((string) $this->id, 8, '0', STR_PAD_LEFT);
    }

    public function dataPagamento(): ?\Illuminate\Support\Carbon
    {
        $payload = $this->payload_gateway ?? [];
        $tx = $payload['transaction'] ?? $payload;
        $pagoEm = $tx['payed_in'] ?? $tx['paid_at'] ?? $tx['confirmed_at'] ?? null;

        if (filled($pagoEm)) {
            return \Illuminate\Support\Carbon::parse($pagoEm);
        }

        return $this->isPago() ? $this->updated_at : null;
    }

    public function identificadorTransacao(): ?string
    {
        if (filled($this->charge_id)) {
            return $this->charge_id;
        }

        $payload = $this->payload_gateway ?? [];

        return $payload['transaction']['id'] ?? $payload['id'] ?? null;
    }

    /**
     * @return BelongsTo<Distribuicao, $this>
     */
    public function distribuicao(): BelongsTo
    {
        return $this->belongsTo(Distribuicao::class);
    }
}
