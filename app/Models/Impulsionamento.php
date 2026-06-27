<?php

namespace App\Models;

use App\Support\WhatsApp;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Impulsionamento extends Model
{
    use HasFactory;

    protected $table = 'impulsionamentos';

    protected $fillable = [
        'gestor_id',
        'titulo',
        'mensagem',
        'imagens',
    ];

    protected function casts(): array
    {
        return [
            'imagens' => 'array',
        ];
    }

    /**
     * @return BelongsTo<Gestor, $this>
     */
    public function gestor(): BelongsTo
    {
        return $this->belongsTo(Gestor::class);
    }

    /**
     * Firmas (EC) destinatárias deste impulsionamento.
     *
     * @return BelongsToMany<Empresa, $this>
     */
    public function empresas(): BelongsToMany
    {
        return $this->belongsToMany(Empresa::class, 'impulsionamento_empresa')
            ->withPivot('enviado_em')
            ->withTimestamps();
    }

    /**
     * @return list<string>
     */
    public function imagensValidas(): array
    {
        return array_values(array_filter(
            (array) $this->imagens,
            static fn ($url) => filled($url),
        ));
    }

    /**
     * Mensagem base (título + corpo), sem as URLs das imagens. Usada no envio
     * automático (Evolution API), onde as imagens são anexadas como mídia real.
     */
    public function mensagemBase(): string
    {
        return '*'.$this->titulo.'*'."\n\n".$this->mensagem;
    }

    /**
     * Texto que será pré-preenchido no WhatsApp. Como o click-to-chat não anexa
     * arquivos, as imagens entram como links no corpo da mensagem.
     */
    public function textoWhatsApp(): string
    {
        $linhas = ['*'.$this->titulo.'*', '', $this->mensagem];

        $imagens = $this->imagensValidas();

        if ($imagens !== []) {
            $linhas[] = '';
            foreach ($imagens as $url) {
                $linhas[] = $url;
            }
        }

        return implode("\n", $linhas);
    }

    public function linkWhatsApp(Empresa $empresa): ?string
    {
        return WhatsApp::link($empresa->telefone, $this->textoWhatsApp());
    }
}
