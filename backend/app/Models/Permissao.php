<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permissao extends Model
{
    protected $table = 'permissoes';

    protected $fillable = [
        'nome',
        'codigo',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'permissao_user', 'permissao_id', 'user_id')
            ->withTimestamps();
    }
}
