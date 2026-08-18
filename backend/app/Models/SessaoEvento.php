<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SessaoEvento extends Model
{
    use HasFactory;

    protected $fillable = [
        'sessao_id',
        'executado_por_id',
        'acao',
        'situacao_anterior',
        'situacao_nova',
        'observacao'
    ];

    public const ACOES = [
        'convocar' => 'Convocação',
        'abrir' => 'Abertura',
        'suspender' => 'Suspensão',
        'retomar' => 'Retomada',
        'encerrar' => 'Encerramento',
        'cancelar' => 'Cancelamento'
    ];

    public function sessao(): BelongsTo
    {
        return $this->belongsTo(Sessao::class);
    }

    public function executadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'executado_por_id')
            ->withTrashed();
    }
}
