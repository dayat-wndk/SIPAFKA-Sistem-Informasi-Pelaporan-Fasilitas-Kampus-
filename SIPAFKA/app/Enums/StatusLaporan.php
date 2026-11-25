<?php

namespace App\Enums;

enum StatusLaporan: string
{
    case MenungguVerifikasi = 'Menunggu Verifikasi';
    case Diverifikasi = 'Diverifikasi';
    case Ditugaskan = 'Ditugaskan';
    case Diproses = 'Diproses';
    case Selesai = 'Selesai';
    case Ditolak = 'Ditolak';

    public function badgeClass(): string
    {
        return match ($this) {
            self::MenungguVerifikasi => 'badge-menunggu',
            self::Diverifikasi => 'badge-diverifikasi',
            self::Ditugaskan => 'badge-ditugaskan',
            self::Diproses => 'badge-diproses',
            self::Selesai => 'badge-selesai',
            self::Ditolak => 'badge-ditolak',
        };
    }

    public static function values(): array
    {
        return array_map(fn (self $status) => $status->value, self::cases());
    }
}
