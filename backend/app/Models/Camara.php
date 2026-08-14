<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Camara extends Model
{
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

    protected function cnpjFormatado(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes): ?string {
                $cnpj = $attributes['cnpj'] ?? null;

                if ($cnpj === null) {
                    return null;
                }

                return preg_replace(
                    '/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/',
                    '$1.$2.$3/$4-$5',
                    $cnpj,
                );
            },
        );
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function vereadores(): HasMany
    {
        return $this->hasMany(Vereador::class);
    }

    public function legislaturas(): HasMany
    {
        return $this->hasMany(Legislatura::class);
    }

    public function tiposProposicao(): HasMany
    {
        return $this->hasMany(TipoProposicao::class);
    }

    public function proposicoes(): HasMany
    {
        return $this->hasMany(Proposicao::class);
    }

    public function unidadesTramitacao(): HasMany
    {
        return $this->hasMany(UnidadeTramitacao::class);
    }
}
