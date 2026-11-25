<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Perbaikan extends Model
{
    protected $table = 'perbaikan';

    protected $fillable = [
        'penugasan_id',
        'tanggal',
        'catatan_progres',
        'hasil',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'datetime',
        ];
    }

    public function penugasan(): BelongsTo
    {
        return $this->belongsTo(Penugasan::class, 'penugasan_id');
    }
}
