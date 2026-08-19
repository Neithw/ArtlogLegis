<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemPauta extends Model
{
    use HasFactory;

    protected $table = 'itens_pauta';

    protected $fillable = [
        'sessao_id',
        'proposicao_id',
        'incluido_por_id',
        'ordem'
    ];

    protected function casts(): array
    {
        return [
            'ordem' => 'integer'
        ];
    }

    public function sessao(): BelongsTo
    {
        return $this->belongsTo(Sessao::class);
    }

    public function proposicao(): BelongsTo
    {
        return $this->belongsTo(Proposicao::class);
    }

    public function incluidoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'incluido_por_id')
            ->withTrashed();
    }
}
