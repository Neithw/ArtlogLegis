<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vereador extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'vereadores';

    protected $fillable = [
        'user_id',
        'camara_id',
        'nome',
        'nome_parlamentar',
        'email_institucional',
        'telefone_institucional',
        'biografia',
        'foto_path',
    ];

    protected function telefoneInstitucionalFormatado(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes): ?string {
                $telefone = $attributes['telefone_institucional'] ?? null;

                if ($telefone === null) {
                    return null;
                }

                return match (strlen($telefone)) {
                    10 => preg_replace(
                        '/^(\d{2})(\d{4})(\d{4})$/',
                        '($1) $2-$3',
                        $telefone
                    ),

                    11 => preg_replace(
                        '/^(\d{2})(\d{5})(\d{4})$/',
                        '($1) $2-$3',
                        $telefone
                    ),

                    default => $telefone,
                };
            },
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)
            ->withTrashed();
    }

    public function camara(): BelongsTo
    {
        return $this->belongsTo(Camara::class);
    }

    public function mandatos(): HasMany
    {
        return $this->hasMany(Mandato::class);
    }
}
