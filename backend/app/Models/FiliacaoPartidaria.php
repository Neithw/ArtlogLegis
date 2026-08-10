<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FiliacaoPartidaria extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'filiacoes_partidarias';

    protected $fillable = [
        'mandato_id',
        'partido_id',
        'data_inicio',
        'data_fim'
    ];

    protected function casts(): array
    {
        return [
            'data_inicio' => 'date',
            'data_fim' => 'date',
        ];
    }

    public function mandato(): BelongsTo
    {
        return $this->belongsTo(Mandato::class)
            ->withTrashed();
    }

    public function partido(): BelongsTo
    {
        return $this->belongsTo(Partido::class)
            ->withTrashed();
    }
}
