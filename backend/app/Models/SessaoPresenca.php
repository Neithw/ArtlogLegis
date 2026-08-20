<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SessaoPresenca extends Model
{
    use HasFactory;

    protected $fillable = [
        'sessao_id',
        'mandato_id',
        'registrado_por_id',
        'atualizado_por_id',
        'situacao',
        'observacao'
    ];

    public const SITUACOES = [
        'presente' => 'Presente',
        'ausente' => 'Ausente',
        'justificada' => 'Ausência justificada'
    ];

    public function sessao(): BelongsTo
    {
        return $this->belongsTo(Sessao::class);
    }

    public function mandato(): BelongsTo
    {
        return $this->belongsTo(Mandato::class)
            ->withTrashed();
    }

    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por_id')
            ->withTrashed();
    }

    public function atualizadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'atualizado_por_id')
            ->withTrashed();
    }
}
