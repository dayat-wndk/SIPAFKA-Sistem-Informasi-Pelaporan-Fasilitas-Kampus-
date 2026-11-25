<?php

namespace App\Models;

use App\Enums\StatusLaporan;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Laporan extends Model
{
    protected $table = 'laporan';

    protected $fillable = [
        'pelapor_id',
        'fasilitas_id',
        'admin_id',
        'judul',
        'deskripsi',
        'foto',
        'tanggal',
        'status',
        'alasan_penolakan',
        'diverifikasi_at',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'diverifikasi_at' => 'datetime',
            'status' => StatusLaporan::class,
        ];
    }

    public function pelapor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pelapor_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function fasilitas(): BelongsTo
    {
        return $this->belongsTo(Fasilitas::class, 'fasilitas_id');
    }

    public function penugasan(): HasOne
    {
        return $this->hasOne(Penugasan::class, 'laporan_id');
    }

    protected function nomorLaporan(): Attribute
    {
        return Attribute::get(
            fn (): string => 'SIP-' . str_pad((string) $this->id, 3, '0', STR_PAD_LEFT)
        );
    }

    protected function fotoUrl(): Attribute
    {
        return Attribute::get(
            fn (): ?string => $this->foto ? asset('storage/' . ltrim($this->foto, '/')) : null
        );
    }
}
