<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class UnidadeTramitacao extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'unidades_tramitacao';

    protected $fillable = [
        'camara_id',
        'nome',
        'sigla',
        'tipo',
        'descricao'
    ];

    public const TIPOS = [
        'secretaria' => 'Secretaria',
        'mesa_diretora' => 'Mesa Diretora',
        'plenario' => 'Plenário',
        'departamento' => 'Departamento',
        'unidade_administrativa' => 'Unidade Administrativa',
        'orgao_externo' => 'Órgão Externo',
        'outro' => 'Outro',
    ];

    public function camara(): BelongsTo
    {
        return $this->belongsTo(Camara::class)
            ->withTrashed();
    }

    public function tramitacoesComoOrigem(): HasMany
    {
        return $this->hasMany(Tramitacao::class, 'unidade_origem_id');
    }

    public function tramitacoesComoDestino(): HasMany
    {
        return $this->hasMany(Tramitacao::class, 'unidade_destino_id');
    }

    public function usuarios(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'unidade_tramitacao_user', 'unidade_tramitacao_id', 'user_id')
            ->withTimestamps();
    }
}
