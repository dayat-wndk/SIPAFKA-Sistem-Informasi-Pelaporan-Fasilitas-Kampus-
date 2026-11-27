<?php

namespace Tests\Feature;

use App\Enums\StatusLaporan;
use App\Models\Fasilitas;
use App\Models\Laporan;
use App\Models\Penugasan;
use App\Models\Perbaikan;
use App\Models\User;
use Database\Seeders\FasilitasSeeder;
use Database\Seeders\Modul1DemoSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TeknisiTugasTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([UserSeeder::class, FasilitasSeeder::class]);
    }

    public function test_tamu_pelapor_admin_dan_akun_nonaktif_ditolak_pada_route_teknisi(): void
    {
        $pelapor = User::where('email', 'nadia@uho.ac.id')->firstOrFail();
        $admin = User::where('email', 'admin.fmipa@uho.ac.id')->firstOrFail();
        $teknisiNonaktif = User::factory()->create([
            'role' => 'teknisi',
            'aktif' => false,
        ]);

        $this->get(route('teknisi.tugas.index'))
            ->assertRedirect(route('login'));

        $this->actingAs($pelapor)
            ->get(route('teknisi.tugas.index'))
            ->assertForbidden();

        $this->actingAs($admin)
            ->get(route('teknisi.tugas.index'))
            ->assertForbidden();

        $this->actingAs($teknisiNonaktif)
            ->get(route('teknisi.tugas.index'))
            ->assertRedirect(route('login'));
    }

    public function test_daftar_tugas_hanya_menampilkan_milik_teknisi_login_serta_mendukung_pencarian_dan_filter(): void
    {
        Storage::fake('public');
        $this->seed(Modul1DemoSeeder::class);

        $andi = User::where('email', 'andi@uho.ac.id')->firstOrFail();

        $response = $this->actingAs($andi)->get(route('teknisi.tugas.index'));
        $response->assertOk()
            ->assertSee('Lampu laboratorium mati')
            ->assertSee('AC ruang kuliah tidak dingin')
            ->assertDontSee('Kran air bocor')
            ->assertDontSee('Proyektor tidak menyala');

        $laporanLampu = Laporan::where('judul', 'Lampu laboratorium mati')->firstOrFail();

        $this->actingAs($andi)
            ->get(route('teknisi.tugas.index', ['q' => $laporanLampu->nomor_laporan]))
            ->assertOk()
            ->assertSee('Lampu laboratorium mati')
            ->assertDontSee('AC ruang kuliah tidak dingin');

        $this->actingAs($andi)
            ->get(route('teknisi.tugas.index', ['status' => StatusLaporan::Diproses->value]))
            ->assertOk()
            ->assertSee('AC ruang kuliah tidak dingin')
            ->assertDontSee('Lampu laboratorium mati');
    }

    public function test_teknisi_mendapat_403_saat_mengakses_memulai_atau_memperbarui_tugas_teknisi_lain(): void
    {
        Storage::fake('public');
        $this->seed(Modul1DemoSeeder::class);

        $andi = User::where('email', 'andi@uho.ac.id')->firstOrFail();
        $siti = User::where('email', 'siti@uho.ac.id')->firstOrFail();
        $nadia = User::where('email', 'nadia@uho.ac.id')->firstOrFail();
        $admin = User::where('email', 'admin.fmipa@uho.ac.id')->firstOrFail();
        $fasilitas = Fasilitas::firstOrFail();

        $laporanSiti = Laporan::where('judul', 'Kran air bocor')->firstOrFail();

        $laporanSitiDitugaskan = Laporan::create([
            'pelapor_id' => $nadia->id,
            'fasilitas_id' => $fasilitas->id,
            'admin_id' => $admin->id,
            'judul' => 'Stopkontak Lab Fisika rusak',
            'deskripsi' => 'Stopkontak tidak mengalirkan listrik saat praktikum berlangsung.',
            'foto' => 'laporan-foto/contoh-kerusakan.png',
            'tanggal' => '2026-11-25',
            'status' => StatusLaporan::Ditugaskan->value,
            'diverifikasi_at' => now(),
        ]);

        Penugasan::create([
            'laporan_id' => $laporanSitiDitugaskan->id,
            'teknisi_id' => $siti->id,
            'admin_id' => $admin->id,
            'tanggal_tugas' => now(),
            'catatan' => 'Periksa jalur MCB.',
        ]);

        $this->actingAs($andi)->get(route('teknisi.tugas.show', $laporanSiti))->assertForbidden();
        $this->actingAs($andi)->get(route('teknisi.tugas.completed', $laporanSiti))->assertForbidden();
        $this->actingAs($andi)->get(route('teknisi.tugas.show', $laporanSitiDitugaskan))->assertForbidden();
        $this->actingAs($andi)->post(route('teknisi.tugas.start', $laporanSitiDitugaskan))->assertForbidden();
        $this->actingAs($andi)->get(route('teknisi.tugas.edit', $laporanSitiDitugaskan))->assertForbidden();
        $this->actingAs($andi)->put(route('teknisi.tugas.update', $laporanSitiDitugaskan), [
            'status' => StatusLaporan::Diproses->value,
            'catatan' => 'Percobaan akses tugas milik teknisi lain.',
        ])->assertForbidden();
    }

    public function test_alur_lengkap_mulai_perbaikan_update_diproses_hingga_selesai_dan_terlihat_di_modul_2(): void
    {
        Storage::fake('public');
        $this->seed(Modul1DemoSeeder::class);

        $andi = User::where('email', 'andi@uho.ac.id')->firstOrFail();
        $nadia = User::where('email', 'nadia@uho.ac.id')->firstOrFail();
        $tugas = Laporan::where('judul', 'Lampu laboratorium mati')->firstOrFail();

        $this->actingAs($andi)
            ->get(route('teknisi.tugas.show', $tugas))
            ->assertOk()
            ->assertSee($tugas->nomor_laporan)
            ->assertSee('Mulai perbaikan')
            ->assertSee('Periksa saklar dan ganti lampu LED yang mati.');

        // 1. Mulai perbaikan: Ditugaskan -> Diproses
        $this->actingAs($andi)
            ->post(route('teknisi.tugas.start', $tugas))
            ->assertRedirect(route('teknisi.tugas.edit', $tugas));

        $tugas->refresh();
        $this->assertSame(StatusLaporan::Diproses, $tugas->status);
        $this->assertDatabaseHas('perbaikan', [
            'penugasan_id' => $tugas->penugasan->id,
            'catatan_progres' => 'Teknisi memulai perbaikan.',
            'hasil' => null,
        ]);

        // 2. Validasi status terlarang & catatan terlalu pendek ditolak
        $this->actingAs($andi)
            ->from(route('teknisi.tugas.edit', $tugas))
            ->put(route('teknisi.tugas.update', $tugas), [
                'status' => StatusLaporan::Ditolak->value,
                'catatan' => 'Pendek',
            ])
            ->assertRedirect(route('teknisi.tugas.edit', $tugas))
            ->assertSessionHasErrors(['status', 'catatan']);

        // 3. Simpan progres Diproses
        $this->actingAs($andi)
            ->put(route('teknisi.tugas.update', $tugas), [
                'status' => StatusLaporan::Diproses->value,
                'catatan' => 'Sedang mengganti dua lampu LED dan memeriksa saklar utama.',
            ])
            ->assertRedirect(route('teknisi.tugas.edit', $tugas));

        $tugas->refresh();
        $this->assertSame(StatusLaporan::Diproses, $tugas->status);

        $this->actingAs($andi)
            ->get(route('teknisi.tugas.edit', $tugas))
            ->assertOk()
            ->assertSee('Sedang mengganti dua lampu LED dan memeriksa saklar utama.')
            ->assertSee('Teknisi memulai perbaikan.');

        // 4. Simpan status Selesai
        $catatanAkhir = 'Dua lampu LED baru telah dipasang dan diuji. Pencahayaan lab kembali normal.';
        $this->actingAs($andi)
            ->put(route('teknisi.tugas.update', $tugas), [
                'status' => StatusLaporan::Selesai->value,
                'catatan' => $catatanAkhir,
            ])
            ->assertRedirect(route('teknisi.tugas.completed', $tugas));

        $tugas->refresh();
        $this->assertSame(StatusLaporan::Selesai, $tugas->status);
        $this->assertDatabaseHas('perbaikan', [
            'penugasan_id' => $tugas->penugasan->id,
            'catatan_progres' => $catatanAkhir,
            'hasil' => $catatanAkhir,
        ]);

        $this->actingAs($andi)
            ->get(route('teknisi.tugas.completed', $tugas))
            ->assertOk()
            ->assertSee('Perbaikan berhasil diselesaikan.')
            ->assertSee($catatanAkhir);

        // 5. Tugas yang sudah Selesai tidak dapat diubah mundur lagi
        $this->actingAs($andi)
            ->put(route('teknisi.tugas.update', $tugas), [
                'status' => StatusLaporan::Diproses->value,
                'catatan' => 'Mencoba mengembalikan status dari selesai ke diproses.',
            ])
            ->assertRedirect(route('teknisi.tugas.completed', $tugas));

        $tugas->refresh();
        $this->assertSame(StatusLaporan::Selesai, $tugas->status);

        // 6. Integrasi dengan Detail Laporan Pelapor (Modul 2)
        $this->actingAs($nadia)
            ->get(route('pelapor.laporan.show', $tugas))
            ->assertOk()
            ->assertSee('Selesai')
            ->assertSee($catatanAkhir)
            ->assertSee('Andi Pratama');
    }
}
