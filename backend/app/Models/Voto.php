<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Voto extends Model
{
    use HasFactory;

    protected $fillable = [
        'votacao_id',
        'mandato_id',
        'registrado_por_id',
        'atualizado_por_id',
        'escolha'
    ];

    public const ESCOLHAS = [
        'favoravel' => 'Favorável',
        'contrario' => 'Contrário',
        'abstencao' => 'Abstenção'
    ];

    public function votacao(): BelongsTo
    {
        return $this->belongsTo(Votacao::class);
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
