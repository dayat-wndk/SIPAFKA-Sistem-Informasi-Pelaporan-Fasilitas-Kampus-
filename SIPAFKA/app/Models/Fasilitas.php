<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fasilitas extends Model
{
    protected $table = 'fasilitas';

    protected $fillable = [
        'nama',
        'gedung',
        'ruang',
        'keterangan',
    ];

    public function laporan(): HasMany
    {
        return $this->hasMany(Laporan::class, 'fasilitas_id');
    }

    protected function kode(): Attribute
    {
        return Attribute::get(
            fn (): string => 'F-' . str_pad((string) $this->id, 3, '0', STR_PAD_LEFT)
        );
    }
}
