<?php

namespace App\Models;

use App\Support\WhatsApp;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Gestor extends Model
{
    use HasFactory;

    protected $table = 'gestores';

    protected $fillable = [
        'user_id',
        'nome',
        'cpf',
        'telefone',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<Impulsionamento, $this>
     */
    public function impulsionamentos(): HasMany
    {
        return $this->hasMany(Impulsionamento::class);
    }

    public function temTelefone(): bool
    {
        return WhatsApp::normalizar($this->telefone) !== null;
    }
}
