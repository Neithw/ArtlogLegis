<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Partido extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'nome',
        'sigla',
        'numero_eleitoral'
    ];

    public function filiacoesPartidarias(): HasMany
    {
        return $this->hasMany(FiliacaoPartidaria::class);
    }
}
