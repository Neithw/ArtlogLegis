<?php

namespace App\Models;

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
