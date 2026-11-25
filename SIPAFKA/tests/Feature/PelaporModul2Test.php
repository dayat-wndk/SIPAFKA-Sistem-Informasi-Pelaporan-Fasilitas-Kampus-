<?php

namespace Tests\Feature;

use App\Enums\StatusLaporan;
use App\Models\Fasilitas;
use App\Models\Laporan;
use App\Models\User;
use Database\Seeders\FasilitasSeeder;
use Database\Seeders\Modul1DemoSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PelaporModul2Test extends TestCase
{
    use RefreshDatabase;

    private function validPngBytes(): string
    {
        return base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg=='
        );
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([UserSeeder::class, FasilitasSeeder::class]);
    }

    public function test_validasi_ketat_store_laporan_untuk_judul_fasilitas_dan_foto(): void
    {
        Storage::fake('public');

        $nadia = User::where('email', 'nadia@uho.ac.id')->firstOrFail();

        $resInvalid = $this->actingAs($nadia)
            ->from(route('pelapor.laporan.create'))
            ->post(route('pelapor.laporan.store'), [
                'judul' => str_repeat('A', 151),
                'deskripsi' => 'Deskripsi singkat',
                'fasilitas_id' => 999999,
                'foto' => UploadedFile::fake()->create('bukan-gambar.txt', 50, 'text/plain'),
            ]);

        $resInvalid->assertRedirect(route('pelapor.laporan.create'))
            ->assertSessionHasErrors(['judul', 'fasilitas_id', 'foto']);

        $this->assertDatabaseCount('laporan', 0);
    }

    public function test_unggah_foto_jpg_valid_berhasil_disimpan_dan_ditampilkan_di_halaman_sukses_dan_detail(): void
    {
        Storage::fake('public');

        $nadia = User::where('email', 'nadia@uho.ac.id')->firstOrFail();
        $fasilitas = Fasilitas::where('nama', 'AC')->firstOrFail();
        $foto = UploadedFile::fake()->image('ac-rusak.jpg', 800, 600);

        $response = $this->actingAs($nadia)->post(route('pelapor.laporan.store'), [
            'judul' => 'AC ruang kuliah bocor',
            'deskripsi' => 'Air menetes dari unit indoor AC di atas papan tulis ruang kuliah.',
            'fasilitas_id' => $fasilitas->id,
            'foto' => $foto,
        ]);

        $laporan = Laporan::where('judul', 'AC ruang kuliah bocor')->firstOrFail();

        $response->assertRedirect(route('pelapor.laporan.sukses', $laporan));
        Storage::disk('public')->assertExists($laporan->foto);
        $this->assertSame(StatusLaporan::MenungguVerifikasi, $laporan->status);

        $this->actingAs($nadia)
            ->get(route('pelapor.laporan.sukses', $laporan))
            ->assertOk()
            ->assertSee($laporan->nomor_laporan)
            ->assertSee('Menunggu Verifikasi')
            ->assertSee($fasilitas->nama)
            ->assertSee($fasilitas->ruang);

        $this->actingAs($nadia)
            ->get(route('pelapor.laporan.show', $laporan))
            ->assertOk()
            ->assertSee($laporan->foto_url, false)
            ->assertSee(basename($laporan->foto))
            ->assertSee('Foto kerusakan')
            ->assertSee('KB');
    }

    public function test_riwayat_mendukung_pencarian_parameter_q_dan_pagination_sepuluh_per_halaman(): void
    {
        Storage::fake('public');

        $nadia = User::where('email', 'nadia@uho.ac.id')->firstOrFail();
        $fasilitas = Fasilitas::firstOrFail();

        for ($i = 1; $i <= 12; $i++) {
            Laporan::create([
                'pelapor_id' => $nadia->id,
                'fasilitas_id' => $fasilitas->id,
                'judul' => sprintf('Kerusakan Fasilitas %02d', $i),
                'deskripsi' => 'Deskripsi kerusakan fasilitas untuk pengujian pagination riwayat.',
                'foto' => 'laporan-foto/contoh.png',
                'tanggal' => now()->subDays(12 - $i)->toDateString(),
                'status' => StatusLaporan::MenungguVerifikasi->value,
            ]);
        }

        $this->actingAs($nadia)
            ->get(route('pelapor.laporan.index'))
            ->assertOk()
            ->assertSee('Menampilkan 1')
            ->assertSee('10 dari 12 laporan')
            ->assertSee('Halaman 1 dari 2')
            ->assertSee('Kerusakan Fasilitas 12')
            ->assertDontSee('Kerusakan Fasilitas 01');

        $this->actingAs($nadia)
            ->get(route('pelapor.laporan.index', ['page' => 2]))
            ->assertOk()
            ->assertSee('Menampilkan 11')
            ->assertSee('12 dari 12 laporan')
            ->assertSee('Halaman 2 dari 2')
            ->assertSee('Kerusakan Fasilitas 01');

        $this->actingAs($nadia)
            ->get(route('pelapor.laporan.index', ['q' => 'Fasilitas 05']))
            ->assertOk()
            ->assertSee('Kerusakan Fasilitas 05')
            ->assertDontSee('Kerusakan Fasilitas 12');
    }

    public function test_detail_laporan_selesai_menampilkan_timeline_lengkap_dan_pelapor_lain_tidak_bisa_mengakses(): void
    {
        Storage::fake('public');
        $this->seed(Modul1DemoSeeder::class);

        $nadia = User::where('email', 'nadia@uho.ac.id')->firstOrFail();
        $rizal = User::where('email', 'rizal@uho.ac.id')->firstOrFail();
        $admin = User::where('email', 'admin.fmipa@uho.ac.id')->firstOrFail();

        $laporanSelesai = Laporan::where('judul', 'Kran air bocor')->firstOrFail();

        $this->actingAs($nadia)
            ->get(route('pelapor.laporan.show', $laporanSelesai))
            ->assertOk()
            ->assertSee('Menunggu Verifikasi')
            ->assertSee('Diverifikasi')
            ->assertSee('Ditugaskan')
            ->assertSee('Diproses')
            ->assertSee('Selesai')
            ->assertSee('Siti Rahma')
            ->assertSee('Kran wastafel telah diganti dan diuji, tidak ada kebocoran.');

        $this->actingAs($rizal)
            ->get(route('pelapor.laporan.show', $laporanSelesai))
            ->assertNotFound();

        $this->actingAs($admin)
            ->get(route('pelapor.laporan.show', $laporanSelesai))
            ->assertForbidden();
    }
}
