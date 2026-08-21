<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mandato extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'vereador_id',
        'legislatura_id',
        'data_inicio',
        'data_fim',
    ];

    protected function casts(): array
    {
        return [
            'data_inicio' => 'date',
            'data_fim' => 'date',
        ];
    }

    public function scopeVigenteEm(
        Builder $query,
        CarbonInterface $data
    ): Builder {
        $dataReferencia = $data->toDateString();

        return $query
            ->whereNull(
                $query->getModel()->getQualifiedDeletedAtColumn()
            )
            ->whereDate('data_inicio', '<=', $dataReferencia)
            ->where(
                fn(Builder $query) => $query
                    ->whereNull('data_fim')
                    ->orWhereDate('data_fim', '>=', $dataReferencia)
            );
    }

    public function estaVigenteEm(CarbonInterface $data): bool
    {
        $dataReferencia = $data->toDateString();

        return ! $this->trashed()
            && $this->data_inicio->toDateString() <= $dataReferencia
            && (
                $this->data_fim === null
                || $this->data_fim->toDateString() >= $dataReferencia
            );
    }

    public function vereador(): BelongsTo
    {
        return $this->belongsTo(Vereador::class);
    }

    public function legislatura(): BelongsTo
    {
        return $this->belongsTo(Legislatura::class)
            ->withTrashed();
    }

    public function filiacoesPartidarias(): HasMany
    {
        return $this->hasMany(FiliacaoPartidaria::class);
    }

    public function ultimaFiliacaoPartidaria(): HasOne
    {
        return $this->hasOne(FiliacaoPartidaria::class)
            ->latestOfMany('data_inicio');
    }

    public function primeiraFiliacaoPartidaria(): HasOne
    {
        return $this->hasOne(FiliacaoPartidaria::class)
            ->oldestOfMany('data_inicio');
    }

    public function proposicoes(): HasMany
    {
        return $this->hasMany(Proposicao::class, 'autor_mandato_id')
            ->withTrashed();
    }

    public function presencas(): HasMany
    {
        return $this->hasMany(SessaoPresenca::class);
    }

    public function votos(): HasMany
    {
        return $this->hasMany(Voto::class);
    }
}
