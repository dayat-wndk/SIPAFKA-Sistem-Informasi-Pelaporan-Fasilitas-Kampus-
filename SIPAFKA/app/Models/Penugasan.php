<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Penugasan extends Model
{
    protected $table = 'penugasan';

    protected $fillable = [
        'laporan_id',
        'teknisi_id',
        'admin_id',
        'tanggal_tugas',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_tugas' => 'datetime',
        ];
    }

    public function laporan(): BelongsTo
    {
        return $this->belongsTo(Laporan::class, 'laporan_id');
    }

    public function teknisi(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teknisi_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function perbaikan(): HasMany
    {
        return $this->hasMany(Perbaikan::class, 'penugasan_id');
    }
}
