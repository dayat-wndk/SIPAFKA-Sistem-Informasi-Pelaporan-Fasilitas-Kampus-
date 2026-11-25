<?php

namespace Database\Seeders;

use App\Enums\StatusLaporan;
use App\Models\Fasilitas;
use App\Models\Laporan;
use App\Models\Penugasan;
use App\Models\Perbaikan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class Modul1DemoSeeder extends Seeder
{
    public function run(): void
    {
        $nadia = User::where('email', 'nadia@uho.ac.id')->first();
        $admin = User::where('email', 'admin.fmipa@uho.ac.id')->first();
        $andi = User::where('email', 'andi@uho.ac.id')->first();
        $siti = User::where('email', 'siti@uho.ac.id')->first();

        if (! $nadia || ! $admin || ! $andi || ! $siti) {
            return;
        }

        // Pastikan file foto contoh tersedia pada disk public
        $fotoContoh = 'laporan-foto/contoh-kerusakan.png';
        if (! Storage::disk('public')->exists($fotoContoh)) {
            $pngBase64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';
            Storage::disk('public')->put($fotoContoh, base64_decode($pngBase64));
        }

        if ($nadia->laporanDibuat()->exists()) {
            return;
        }

        $fasilitasProyektor = Fasilitas::where('nama', 'Proyektor')->where('ruang', 'Ruang kuliah B.201')->first() ?? Fasilitas::first();
        $fasilitasAc = Fasilitas::where('nama', 'AC')->where('ruang', 'Ruang kuliah B.204')->first() ?? Fasilitas::first();
        $fasilitasLampu = Fasilitas::where('nama', 'Lampu')->first() ?? Fasilitas::first();
        $fasilitasKursi = Fasilitas::where('nama', 'Kursi kuliah')->first() ?? Fasilitas::first();
        $fasilitasKran = Fasilitas::where('nama', 'Kran air')->first() ?? Fasilitas::first();
        $fasilitasJalan = Fasilitas::where('nama', 'Lampu jalan')->first() ?? Fasilitas::first();

        // 1. Ditolak
        Laporan::create([
            'pelapor_id' => $nadia->id,
            'fasilitas_id' => $fasilitasJalan->id,
            'admin_id' => $admin->id,
            'judul' => 'Lampu jalan padam',
            'deskripsi' => 'Lampu penerangan di area luar gedung tidak menyala sejak dua malam terakhir.',
            'foto' => $fotoContoh,
            'tanggal' => '2026-11-19',
            'status' => StatusLaporan::Ditolak->value,
            'alasan_penolakan' => 'Lokasi pada foto belum sesuai dengan ruang yang disebutkan dalam laporan. Silakan buat laporan baru dengan lokasi dan foto fasilitas yang sesuai.',
            'diverifikasi_at' => '2026-11-19 11:00:00',
        ]);

        // 2. Selesai
        $laporanSelesai = Laporan::create([
            'pelapor_id' => $nadia->id,
            'fasilitas_id' => $fasilitasKran->id,
            'admin_id' => $admin->id,
            'judul' => 'Kran air bocor',
            'deskripsi' => 'Kran wastafel di toilet lantai 1 terus menetes meski sudah ditutup rapat.',
            'foto' => $fotoContoh,
            'tanggal' => '2026-11-20',
            'status' => StatusLaporan::Selesai->value,
            'diverifikasi_at' => '2026-11-20 09:30:00',
        ]);

        $penugasanSelesai = Penugasan::create([
            'laporan_id' => $laporanSelesai->id,
            'teknisi_id' => $siti->id,
            'admin_id' => $admin->id,
            'tanggal_tugas' => '2026-11-20 10:15:00',
            'catatan' => 'Ganti seal atau unit kran bila ulir sudah aus.',
        ]);

        Perbaikan::create([
            'penugasan_id' => $penugasanSelesai->id,
            'tanggal' => '2026-11-20 13:00:00',
            'catatan_progres' => 'Pengecekan seal kran wastafel.',
            'hasil' => null,
        ]);

        Perbaikan::create([
            'penugasan_id' => $penugasanSelesai->id,
            'tanggal' => '2026-11-20 15:20:00',
            'catatan_progres' => 'Penggantian katup dan seal kran selesai.',
            'hasil' => 'Kran wastafel telah diganti dan diuji, tidak ada kebocoran.',
        ]);

        // 3. Diverifikasi
        Laporan::create([
            'pelapor_id' => $nadia->id,
            'fasilitas_id' => $fasilitasKursi->id,
            'admin_id' => $admin->id,
            'judul' => 'Kursi kuliah patah',
            'deskripsi' => 'Sandaran dan meja lipat pada kursi kuliah di baris depan patah sehingga tidak dapat digunakan.',
            'foto' => $fotoContoh,
            'tanggal' => '2026-11-22',
            'status' => StatusLaporan::Diverifikasi->value,
            'diverifikasi_at' => '2026-11-22 10:05:00',
        ]);

        // 4. Ditugaskan
        $laporanDitugaskan = Laporan::create([
            'pelapor_id' => $nadia->id,
            'fasilitas_id' => $fasilitasLampu->id,
            'admin_id' => $admin->id,
            'judul' => 'Lampu laboratorium mati',
            'deskripsi' => 'Dua lampu LED di sisi barat Lab. Kimia Dasar padam sehingga pencahayaan kurang saat praktikum.',
            'foto' => $fotoContoh,
            'tanggal' => '2026-11-23',
            'status' => StatusLaporan::Ditugaskan->value,
            'diverifikasi_at' => '2026-11-23 08:45:00',
        ]);

        Penugasan::create([
            'laporan_id' => $laporanDitugaskan->id,
            'teknisi_id' => $andi->id,
            'admin_id' => $admin->id,
            'tanggal_tugas' => '2026-11-23 09:30:00',
            'catatan' => 'Periksa saklar dan ganti lampu LED yang mati.',
        ]);

        // 5. Diproses
        $laporanDiproses = Laporan::create([
            'pelapor_id' => $nadia->id,
            'fasilitas_id' => $fasilitasAc->id,
            'admin_id' => $admin->id,
            'judul' => 'AC ruang kuliah tidak dingin',
            'deskripsi' => 'AC menyala tetapi tidak mengeluarkan udara dingin. Kondisi ruangan terasa panas saat perkuliahan berlangsung.',
            'foto' => $fotoContoh,
            'tanggal' => '2026-11-24',
            'status' => StatusLaporan::Diproses->value,
            'diverifikasi_at' => '2026-11-24 09:10:00',
        ]);

        $penugasanDiproses = Penugasan::create([
            'laporan_id' => $laporanDiproses->id,
            'teknisi_id' => $andi->id,
            'admin_id' => $admin->id,
            'tanggal_tugas' => '2026-11-24 10:00:00',
            'catatan' => 'Periksa pendinginan AC dan lakukan perbaikan sesuai kondisi unit.',
        ]);

        Perbaikan::create([
            'penugasan_id' => $penugasanDiproses->id,
            'tanggal' => '2026-11-24 14:00:00',
            'catatan_progres' => 'Penugasan diterima dari Admin FMIPA dan mulai pembersihan filter.',
            'hasil' => null,
        ]);

        Perbaikan::create([
            'penugasan_id' => $penugasanDiproses->id,
            'tanggal' => '2026-11-25 10:15:00',
            'catatan_progres' => 'Pembersihan filter selesai. Teknisi sedang memeriksa tekanan refrigeran sebelum pengujian akhir.',
            'hasil' => null,
        ]);

        // 6. Menunggu Verifikasi
        Laporan::create([
            'pelapor_id' => $nadia->id,
            'fasilitas_id' => $fasilitasProyektor->id,
            'judul' => 'Proyektor tidak menyala',
            'deskripsi' => 'Proyektor tidak merespons saat tombol daya ditekan. Perkuliahan di ruang B.201 terganggu.',
            'foto' => $fotoContoh,
            'tanggal' => '2026-11-25',
            'status' => StatusLaporan::MenungguVerifikasi->value,
        ]);
    }
}
