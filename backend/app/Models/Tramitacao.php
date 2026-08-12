<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tramitacao extends Model
{
    use HasFactory;

    protected $table = 'tramitacoes';

    protected $fillable = [
        'proposicao_id',
        'unidade_origem_id',
        'unidade_destino_id',
        'encaminhado_por_id',
        'recebido_por_id',
        'data_encaminhamento',
        'data_recebimento',
        'despacho',
    ];

    protected function casts(): array
    {
        return [
            'data_encaminhamento' => 'datetime',
            'data_recebimento' => 'datetime'
        ];
    }

    public function proposicao(): BelongsTo
    {
        return $this->belongsTo(Proposicao::class)
            ->withTrashed();
    }

    public function unidadeOrigem(): BelongsTo
    {
        return $this->belongsTo(UnidadeTramitacao::class, 'unidade_origem_id')
            ->withTrashed();
    }

    public function unidadeDestino(): BelongsTo
    {
        return $this->belongsTo(UnidadeTramitacao::class, 'unidade_destino_id')
            ->withTrashed();
    }

    public function encaminhadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'encaminhado_por_id')
            ->withTrashed();
    }

    public function recebidoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recebido_por_id')
            ->withTrashed();
    }
}
