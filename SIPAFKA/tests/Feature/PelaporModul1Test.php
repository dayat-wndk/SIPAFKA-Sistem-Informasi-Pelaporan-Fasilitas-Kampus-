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

class PelaporModul1Test extends TestCase
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

    public function test_dashboard_pelapor_menampilkan_statistik_dan_tiga_laporan_terbaru_milik_sendiri(): void
    {
        Storage::fake('public');
        $this->seed(Modul1DemoSeeder::class);

        $nadia = User::where('email', 'nadia@uho.ac.id')->firstOrFail();
        $rizal = User::where('email', 'rizal@uho.ac.id')->firstOrFail();
        $fasilitas = Fasilitas::firstOrFail();

        Laporan::create([
            'pelapor_id' => $rizal->id,
            'fasilitas_id' => $fasilitas->id,
            'judul' => 'Laporan milik Rizal',
            'deskripsi' => 'Deskripsi kerusakan milik Rizal.',
            'foto' => 'laporan-foto/rizal.png',
            'tanggal' => '2026-11-25',
            'status' => StatusLaporan::MenungguVerifikasi->value,
        ]);

        $response = $this->actingAs($nadia)->get(route('pelapor.dashboard'));

        $response->assertOk()
            ->assertViewHas('totalLaporan', 6)
            ->assertViewHas('menungguVerifikasi', 1)
            ->assertViewHas('dalamPenanganan', 3)
            ->assertViewHas('selesai', 1)
            ->assertSee('Proyektor tidak menyala')
            ->assertSee('AC ruang kuliah tidak dingin')
            ->assertSee('Lampu laboratorium mati')
            ->assertDontSee('Laporan milik Rizal');
    }

    public function test_halaman_buat_laporan_menampilkan_daftar_fasilitas_unik_dan_json(): void
    {
        $nadia = User::where('email', 'nadia@uho.ac.id')->firstOrFail();

        $this->actingAs($nadia)
            ->get(route('pelapor.laporan.create'))
            ->assertOk()
            ->assertSee('Buat laporan kerusakan')
            ->assertSee('fasilitasDataJson')
            ->assertSee('Ruang kuliah B.204');
    }

    public function test_pelapor_dapat_mengirim_laporan_baru_dengan_status_menunggu_verifikasi(): void
    {
        Storage::fake('public');

        $nadia = User::where('email', 'nadia@uho.ac.id')->firstOrFail();
        $fasilitas = Fasilitas::where('nama', 'Proyektor')->firstOrFail();
        $foto = UploadedFile::fake()->createWithContent('proyektor.png', $this->validPngBytes());

        $response = $this->actingAs($nadia)->post(route('pelapor.laporan.store'), [
            'judul' => 'Proyektor ruang B.201 buram',
            'deskripsi' => 'Lensa proyektor sangat buram sehingga materi kuliah tidak terbaca.',
            'fasilitas_id' => $fasilitas->id,
            'foto' => $foto,
        ]);

        $laporan = Laporan::where('judul', 'Proyektor ruang B.201 buram')->first();
        $this->assertNotNull($laporan);
        $this->assertSame($nadia->id, $laporan->pelapor_id);
        $this->assertSame(StatusLaporan::MenungguVerifikasi, $laporan->status);
        $this->assertNull($laporan->admin_id);
        $this->assertNull($laporan->alasan_penolakan);

        Storage::disk('public')->assertExists($laporan->foto);

        $response->assertRedirect(route('pelapor.laporan.sukses', $laporan));

        $this->actingAs($nadia)
            ->get(route('pelapor.laporan.sukses', $laporan))
            ->assertOk()
            ->assertSee($laporan->nomor_laporan)
            ->assertSee('Proyektor ruang B.201 buram');
    }

    public function test_validasi_gagal_menampilkan_pesan_indonesia_dan_pertahankan_input_lama(): void
    {
        $nadia = User::where('email', 'nadia@uho.ac.id')->firstOrFail();

        $response = $this->actingAs($nadia)
            ->from(route('pelapor.laporan.create'))
            ->post(route('pelapor.laporan.store'), [
                'judul' => 'AC bermasalah',
                'deskripsi' => 'Pendek',
                'fasilitas_id' => '',
            ]);

        $response->assertRedirect(route('pelapor.laporan.create'))
            ->assertSessionHasErrors(['deskripsi', 'fasilitas_id', 'foto'])
            ->assertSessionHasInput('judul', 'AC bermasalah');
    }

    public function test_unggah_foto_pdf_atau_lebih_dari_5mb_ditolak(): void
    {
        Storage::fake('public');

        $nadia = User::where('email', 'nadia@uho.ac.id')->firstOrFail();
        $fasilitas = Fasilitas::firstOrFail();

        $pdf = UploadedFile::fake()->create('dokumen.pdf', 200, 'application/pdf');
        $resPdf = $this->actingAs($nadia)
            ->from(route('pelapor.laporan.create'))
            ->post(route('pelapor.laporan.store'), [
                'judul' => 'Tes unggah PDF',
                'deskripsi' => 'Deskripsi kerusakan minimal sepuluh karakter.',
                'fasilitas_id' => $fasilitas->id,
                'foto' => $pdf,
            ]);

        $resPdf->assertRedirect(route('pelapor.laporan.create'))
            ->assertSessionHasErrors('foto');

        $oversize = UploadedFile::fake()->createWithContent(
            'besar.png',
            $this->validPngBytes() . str_repeat('0', 5125 * 1024)
        );
        $resBig = $this->actingAs($nadia)
            ->from(route('pelapor.laporan.create'))
            ->post(route('pelapor.laporan.store'), [
                'judul' => 'Tes unggah kebesaran',
                'deskripsi' => 'Deskripsi kerusakan minimal sepuluh karakter.',
                'fasilitas_id' => $fasilitas->id,
                'foto' => $oversize,
            ]);

        $resBig->assertRedirect(route('pelapor.laporan.create'))
            ->assertSessionHasErrors('foto');

        $this->assertDatabaseCount('laporan', 0);
    }

    public function test_riwayat_mendukung_pencarian_nomor_dan_filter_status_serta_isolasi_pelapor(): void
    {
        Storage::fake('public');
        $this->seed(Modul1DemoSeeder::class);

        $nadia = User::where('email', 'nadia@uho.ac.id')->firstOrFail();
        $rizal = User::where('email', 'rizal@uho.ac.id')->firstOrFail();

        $laporanAc = Laporan::where('judul', 'AC ruang kuliah tidak dingin')->firstOrFail();

        $resNomor = $this->actingAs($nadia)->get(route('pelapor.laporan.index', [
            'cari' => $laporanAc->nomor_laporan,
        ]));
        $resNomor->assertOk()
            ->assertSee('AC ruang kuliah tidak dingin')
            ->assertDontSee('Proyektor tidak menyala');

        $resFilter = $this->actingAs($nadia)->get(route('pelapor.laporan.index', [
            'status' => StatusLaporan::Ditolak->value,
        ]));
        $resFilter->assertOk()
            ->assertSee('Lampu jalan padam')
            ->assertDontSee('AC ruang kuliah tidak dingin');

        $resRizal = $this->actingAs($rizal)->get(route('pelapor.laporan.index'));
        $resRizal->assertOk()
            ->assertDontSee('AC ruang kuliah tidak dingin')
            ->assertSee('Belum ada laporan kerusakan yang tercatat.');
    }

    public function test_detail_laporan_menampilkan_timeline_dan_alasan_penolakan_serta_404_untuk_pelapor_lain(): void
    {
        Storage::fake('public');
        $this->seed(Modul1DemoSeeder::class);

        $nadia = User::where('email', 'nadia@uho.ac.id')->firstOrFail();
        $rizal = User::where('email', 'rizal@uho.ac.id')->firstOrFail();

        $laporanDiproses = Laporan::where('judul', 'AC ruang kuliah tidak dingin')->firstOrFail();
        $laporanDitolak = Laporan::where('judul', 'Lampu jalan padam')->firstOrFail();

        $this->actingAs($nadia)
            ->get(route('pelapor.laporan.show', $laporanDiproses))
            ->assertOk()
            ->assertSee('Andi Pratama')
            ->assertSee('Pembersihan filter selesai. Teknisi sedang memeriksa tekanan refrigeran sebelum pengujian akhir.')
            ->assertSee('Perkembangan laporan');

        $this->actingAs($nadia)
            ->get(route('pelapor.laporan.show', $laporanDitolak))
            ->assertOk()
            ->assertSee('Alasan penolakan')
            ->assertSee('Lokasi pada foto belum sesuai dengan ruang yang disebutkan dalam laporan.');

        $this->actingAs($rizal)
            ->get(route('pelapor.laporan.show', $laporanDiproses))
            ->assertNotFound();

        $this->actingAs($rizal)
            ->get(route('pelapor.laporan.sukses', $laporanDiproses))
            ->assertNotFound();
    }

    public function test_admin_dan_teknisi_mendapat_403_pada_route_pelapor(): void
    {
        $admin = User::where('email', 'admin.fmipa@uho.ac.id')->firstOrFail();
        $teknisi = User::where('email', 'andi@uho.ac.id')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('pelapor.dashboard'))
            ->assertForbidden();

        $this->actingAs($teknisi)
            ->get(route('pelapor.dashboard'))
            ->assertForbidden();
    }

    public function test_tamu_dan_akun_nonaktif_diarahkan_ke_halaman_login(): void
    {
        $this->get(route('pelapor.dashboard'))
            ->assertRedirect(route('login'));

        $nonaktif = User::factory()->create([
            'role' => 'pelapor',
            'aktif' => false,
        ]);

        $this->actingAs($nonaktif)
            ->get(route('pelapor.dashboard'))
            ->assertRedirect(route('login'));
    }
}
