<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proposicao extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'proposicoes';

    protected $fillable = [
        'camara_id',
        'legislatura_id',
        'tipo_proposicao_id',
        'autor_mandato_id',
        'criado_por_id',
        'ementa',
        'texto_integral',
        'assunto',
        'area_tematica',
        'palavras_chave',
        'situacao',
    ];

    protected function casts(): array
    {
        return [
            'palavras_chave' => 'array',
        ];
    }

    public function camara(): BelongsTo
    {
        return $this->belongsTo(Camara::class)
            ->withTrashed();
    }

    public function legislatura(): BelongsTo
    {
        return $this->belongsTo(Legislatura::class)
            ->withTrashed();
    }

    public function tipoProposicao(): BelongsTo
    {
        return $this->belongsTo(TipoProposicao::class)
            ->withTrashed();
    }

    public function autorMandato(): BelongsTo
    {
        return $this->belongsTo(Mandato::class, 'autor_mandato_id')
            ->withTrashed();
    }

    public function criadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'criado_por_id')
            ->withTrashed();
    }
}
