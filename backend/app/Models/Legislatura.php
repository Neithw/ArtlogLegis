<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Legislatura extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'camara_id',
        'numero',
        'data_inicio',
        'data_fim',
    ];

    protected function casts(): array
    {
        return [
            'numero' => 'integer',
            'data_inicio' => 'date',
            'data_fim' => 'date'
        ];
    }

    public function camara(): BelongsTo
    {
        return $this->belongsTo(Camara::class);
    }

    public function mandatos(): HasMany
    {
        return $this->hasMany(Mandato::class);
    }

    public function proposicoes(): HasMany
    {
        return $this->hasMany(Proposicao::class);
    }

    public function sessoes(): HasMany
    {
        return $this->hasMany(Sessao::class);
    }
}
