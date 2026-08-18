<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sessao extends Model
{
    use HasFactory;

    protected $table = 'sessoes';

    protected $fillable = [
        'camara_id',
        'legislatura_id',
        'criado_por_id',
        'data_hora_inicio_previsto',
        'tipo',
        'local'
    ];

    public const TIPOS = [
        'ordinaria' => 'Ordinária',
        'extraordinaria' => 'Extraordinária',
        'solene' => 'Solene',
        'especial' => 'Especial',
        'audiencia_publica' => 'Audiência Pública'
    ];

    public const SITUACOES = [
        'em_preparacao' => 'Em Preparação',
        'convocada' => 'Convocada',
        'aberta' => 'Aberta',
        'suspensa' => 'Suspensa',
        'encerrada' => 'Encerrada',
        'cancelada' => 'Cancelada'
    ];

    protected function casts(): array
    {
        return [
            'numero' => 'integer',
            'ano' => 'integer',
            'data_hora_inicio_previsto' => 'datetime'
        ];
    }

    public function camara(): BelongsTo
    {
        return $this->belongsTo(Camara::class);
    }

    public function legislatura(): BelongsTo
    {
        return $this->belongsTo(Legislatura::class)
            ->withTrashed();
    }

    public function criadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'criado_por_id')
            ->withTrashed();
    }

    public function eventos(): HasMany
    {
        return $this->hasMany(SessaoEvento::class);
    }
}
