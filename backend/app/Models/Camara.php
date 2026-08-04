<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Camara extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nome',
        'cnpj',
        'ativo',
    ];

    protected function casts(): array
    {
        return [
            'ativo' => 'boolean',
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function legislaturas(): HasMany
    {
        return $this->hasMany(Legislatura::class);
    }
}
