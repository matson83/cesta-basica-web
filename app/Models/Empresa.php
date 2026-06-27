<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Empresa extends Model
{
    use HasFactory;

    public const TIPO_CPF = 'cpf';

    public const TIPO_CNPJ = 'cnpj';

    protected $table = 'empresas';

    protected $fillable = [
        'nome_fantasia',
        'razao_social',
        'tipo_documento',
        'documento',
        'email',
        'telefone',
        'bairro',
        'cidade',
        'uf',
        'endereco',
        'confrapix_token',
        'confrapix_base_url',
        'ativo',
    ];

    protected $hidden = [
        'confrapix_token',
    ];

    protected function casts(): array
    {
        return [
            'confrapix_token' => 'encrypted',
            'ativo' => 'boolean',
        ];
    }

    /**
     * Monta a configuração do gateway Confrapix para esta firma, mesclando os
     * endpoints/timeout padrão (config/services) com o token e base_url próprios.
     *
     * @return array<string, mixed>
     */
    public function confrapixConfig(): array
    {
        $base = (array) config('services.confrapix', []);

        return array_merge($base, array_filter([
            'token' => $this->confrapix_token,
            'base_url' => $this->confrapix_base_url,
        ], fn ($value) => filled($value)));
    }

    public function temTokenConfrapix(): bool
    {
        return filled($this->confrapix_token);
    }

    /**
     * @return array<string, string>
     */
    public static function tiposDocumento(): array
    {
        return [
            self::TIPO_CNPJ => 'CNPJ',
            self::TIPO_CPF => 'CPF',
        ];
    }

    public function documentoLabel(): string
    {
        return self::tiposDocumento()[$this->tipo_documento] ?? 'Documento';
    }

    /**
     * @return HasMany<User, $this>
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * @return HasMany<Produto, $this>
     */
    public function produtos(): HasMany
    {
        return $this->hasMany(Produto::class);
    }

    /**
     * @return HasMany<Cesta, $this>
     */
    public function cestas(): HasMany
    {
        return $this->hasMany(Cesta::class);
    }

    /**
     * @return HasMany<Familia, $this>
     */
    public function familias(): HasMany
    {
        return $this->hasMany(Familia::class);
    }

    /**
     * @return HasMany<Distribuicao, $this>
     */
    public function distribuicoes(): HasMany
    {
        return $this->hasMany(Distribuicao::class);
    }

    /**
     * @return HasMany<Pagamento, $this>
     */
    public function pagamentos(): HasMany
    {
        return $this->hasMany(Pagamento::class);
    }
}
