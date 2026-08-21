<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Votacao extends Model
{
    use HasFactory;

    protected $table = 'votacoes';

    protected $fillable = [
        'item_pauta_id',
        'aberta_por_id',
        'tipo',
        'criterio_aprovacao',
        'aberta_em',
        'observacao'
    ];

    public const TIPOS = [
        'nominal' => 'Nominal'
    ];

    public const CRITERIOS_APROVACAO = [
        'maioria_simples' => 'Maioria Simples'
    ];

    public const SITUACOES = [
        'aberta' => 'Aberta',
        'encerrada' => 'Encerrada',
        'cancelada' => 'Cancelada'
    ];

    public const RESULTADOS = [
        'aprovada' => 'Aprovada',
        'rejeitada' => 'Rejeitada',
        'empate' => 'Empate',
        'sem_decisao' => 'Sem decisão',
    ];

    protected function casts(): array
    {
        return [
            'aberta_em' => 'datetime',
            'encerrada_em' => 'datetime',
            'cancelada_em' => 'datetime'
        ];
    }

    public function itemPauta(): BelongsTo
    {
        return $this->belongsTo(ItemPauta::class);
    }

    public function abertaPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'aberta_por_id')
            ->withTrashed();
    }

    public function encerradaPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'encerrada_por_id')
            ->withTrashed();
    }

    public function canceladaPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelada_por_id')
            ->withTrashed();
    }

    public function votos(): HasMany
    {
        return $this->hasMany(Voto::class);
    }
}
