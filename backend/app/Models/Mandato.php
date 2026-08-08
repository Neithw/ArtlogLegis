<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

    public function vereador(): BelongsTo
    {
        return $this->belongsTo(Vereador::class)
            ->withTrashed();
    }

    public function legislatura(): BelongsTo
    {
        return $this->belongsTo(Legislatura::class)
            ->withTrashed();
    }
}
