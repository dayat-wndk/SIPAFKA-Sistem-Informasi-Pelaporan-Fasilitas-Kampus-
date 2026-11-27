<?php

namespace Tests\Feature;

use App\Enums\StatusLaporan;
use App\Models\Fasilitas;
use App\Models\Laporan;
use App\Models\Penugasan;
use App\Models\User;
use Database\Seeders\FasilitasSeeder;
use Database\Seeders\Modul1DemoSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminVerifikasiPenugasanTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([UserSeeder::class, FasilitasSeeder::class]);
    }

    public function test_tamu_pelapor_teknisi_dan_admin_nonaktif_tidak_dapat_mengakses_route_modul_3(): void
    {
        Storage::fake('public');
        $this->seed(Modul1DemoSeeder::class);

        $laporan = Laporan::firstOrFail();
        $pelapor = User::where('email', 'nadia@uho.ac.id')->firstOrFail();
        $teknisi = User::where('email', 'andi@uho.ac.id')->firstOrFail();
        $adminNonaktif = User::factory()->create([
            'role' => 'admin',
            'aktif' => false,
        ]);

        $this->get(route('admin.laporan.index'))->assertRedirect(route('login'));
        $this->get(route('admin.laporan.show', $laporan))->assertRedirect(route('login'));

        foreach ([$pelapor, $teknisi] as $nonAdmin) {
            $this->actingAs($nonAdmin)->get(route('admin.laporan.index'))->assertForbidden();
            $this->actingAs($nonAdmin)->get(route('admin.laporan.show', $laporan))->assertForbidden();
            $this->actingAs($nonAdmin)->post(route('admin.laporan.approve', $laporan))->assertForbidden();
            $this->actingAs($nonAdmin)->post(route('admin.laporan.reject', $laporan), ['alasan_penolakan' => 'Ditolak'])->assertForbidden();
            $this->actingAs($nonAdmin)->get(route('admin.laporan.assign', $laporan))->assertForbidden();
            $this->actingAs($nonAdmin)->post(route('admin.laporan.assign.store', $laporan), ['teknisi_id' => $teknisi->id])->assertForbidden();
        }

        $this->actingAs($adminNonaktif)
            ->get(route('admin.laporan.index'))
            ->assertRedirect(route('login'));
    }

    public function test_halaman_laporan_masuk_mendukung_pencarian_filter_status_dan_fasilitas(): void
    {
        Storage::fake('public');
        $this->seed(Modul1DemoSeeder::class);

        $admin = User::where('email', 'admin.fmipa@uho.ac.id')->firstOrFail();
        $laporanProyektor = Laporan::where('judul', 'Proyektor tidak menyala')->firstOrFail();
        $laporanKursi = Laporan::where('judul', 'Kursi kuliah patah')->firstOrFail();

        // Tampilan awal menampilkan semua laporan dan tombol aksi sesuai status
        $this->actingAs($admin)
            ->get(route('admin.laporan.index'))
            ->assertOk()
            ->assertSee('Proyektor tidak menyala')
            ->assertSee('Kursi kuliah patah')
            ->assertSee('Tinjau')
            ->assertSee('Tugaskan');

        // Pencarian nomor laporan
        $this->actingAs($admin)
            ->get(route('admin.laporan.index', ['cari' => $laporanProyektor->nomor_laporan]))
            ->assertOk()
            ->assertSee('Proyektor tidak menyala')
            ->assertDontSee('Kursi kuliah patah');

        // Filter status Diverifikasi
        $this->actingAs($admin)
            ->get(route('admin.laporan.index', ['status' => StatusLaporan::Diverifikasi->value]))
            ->assertOk()
            ->assertSee('Kursi kuliah patah')
            ->assertDontSee('Proyektor tidak menyala');

        // Filter fasilitas
        $this->actingAs($admin)
            ->get(route('admin.laporan.index', ['fasilitas_id' => $laporanKursi->fasilitas_id]))
            ->assertOk()
            ->assertSee('Kursi kuliah patah')
            ->assertDontSee('Proyektor tidak menyala');
    }

    public function test_admin_dapat_memverifikasi_laporan_menunggu_verifikasi_dan_terlihat_di_timeline_pelapor(): void
    {
        Storage::fake('public');
        $this->seed(Modul1DemoSeeder::class);

        $admin = User::where('email', 'admin.fmipa@uho.ac.id')->firstOrFail();
        $pelapor = User::where('email', 'nadia@uho.ac.id')->firstOrFail();
        $laporan = Laporan::where('status', StatusLaporan::MenungguVerifikasi->value)->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.laporan.show', $laporan))
            ->assertOk()
            ->assertSee('Verifikasi laporan')
            ->assertSee('Tolak laporan');

        $response = $this->actingAs($admin)
            ->post(route('admin.laporan.approve', $laporan), [
                'alasan_penolakan' => 'Kerusakan sesuai deskripsi.',
            ]);

        $response->assertRedirect(route('admin.laporan.index'))
            ->assertSessionHas('sukses');

        $laporan->refresh();
        $this->assertSame(StatusLaporan::Diverifikasi, $laporan->status);
        $this->assertSame($admin->id, $laporan->admin_id);
        $this->assertNotNull($laporan->diverifikasi_at);
        $this->assertNull($laporan->alasan_penolakan);

        // Integrasi Modul 2 (Pelapor): timeline otomatis menampilkan tahap Diverifikasi oleh Admin FMIPA
        $this->actingAs($pelapor)
            ->get(route('pelapor.laporan.show', $laporan))
            ->assertOk()
            ->assertSee('Diverifikasi')
            ->assertSee($admin->name);
    }

    public function test_tolak_laporan_tanpa_alasan_gagal_validasi_dan_status_tetap_menunggu(): void
    {
        Storage::fake('public');
        $this->seed(Modul1DemoSeeder::class);

        $admin = User::where('email', 'admin.fmipa@uho.ac.id')->firstOrFail();
        $laporan = Laporan::where('status', StatusLaporan::MenungguVerifikasi->value)->firstOrFail();

        $response = $this->actingAs($admin)
            ->from(route('admin.laporan.show', $laporan))
            ->post(route('admin.laporan.reject', $laporan), [
                'alasan_penolakan' => '   ',
            ]);

        $response->assertRedirect(route('admin.laporan.show', $laporan))
            ->assertSessionHasErrors('alasan_penolakan');

        $laporan->refresh();
        $this->assertSame(StatusLaporan::MenungguVerifikasi, $laporan->status);
        $this->assertNull($laporan->alasan_penolakan);
    }

    public function test_admin_dapat_menolak_laporan_dengan_alasan_dan_menampilkan_halaman_ditolak(): void
    {
        Storage::fake('public');
        $this->seed(Modul1DemoSeeder::class);

        $admin = User::where('email', 'admin.fmipa@uho.ac.id')->firstOrFail();
        $pelapor = User::where('email', 'nadia@uho.ac.id')->firstOrFail();
        $laporan = Laporan::where('status', StatusLaporan::MenungguVerifikasi->value)->firstOrFail();
        $alasan = 'Lokasi pada foto belum sesuai dengan ruang yang disebutkan dalam laporan.';

        $response = $this->actingAs($admin)
            ->post(route('admin.laporan.reject', $laporan), [
                'alasan_penolakan' => $alasan,
            ]);

        $response->assertRedirect(route('admin.laporan.rejected', $laporan));

        $laporan->refresh();
        $this->assertSame(StatusLaporan::Ditolak, $laporan->status);
        $this->assertSame($admin->id, $laporan->admin_id);
        $this->assertSame($alasan, $laporan->alasan_penolakan);
        $this->assertNotNull($laporan->diverifikasi_at);

        $this->actingAs($admin)
            ->get(route('admin.laporan.rejected', $laporan))
            ->assertOk()
            ->assertSee('Laporan ditolak')
            ->assertSee($alasan)
            ->assertSee('Kembali ke laporan masuk');

        $this->actingAs($admin)
            ->get(route('admin.laporan.show', $laporan))
            ->assertRedirect(route('admin.laporan.rejected', $laporan));

        // Integrasi Modul 2 (Pelapor): alasan penolakan tampil pada halaman detail laporan pelapor
        $this->actingAs($pelapor)
            ->get(route('pelapor.laporan.show', $laporan))
            ->assertOk()
            ->assertSee($alasan);
    }

    public function test_admin_dapat_menugaskan_teknisi_aktif_pada_laporan_diverifikasi(): void
    {
        Storage::fake('public');
        $this->seed(Modul1DemoSeeder::class);

        $admin = User::where('email', 'admin.fmipa@uho.ac.id')->firstOrFail();
        $pelapor = User::where('email', 'nadia@uho.ac.id')->firstOrFail();
        $teknisi = User::where('email', 'andi@uho.ac.id')->firstOrFail();
        $laporan = Laporan::where('status', StatusLaporan::Diverifikasi->value)->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.laporan.assign', $laporan))
            ->assertOk()
            ->assertSee('Tentukan penanggung jawab')
            ->assertSee('Andi Pratama')
            ->assertDontSee('Nadia Putri — Teknisi');

        $response = $this->actingAs($admin)
            ->post(route('admin.laporan.assign.store', $laporan), [
                'teknisi_id' => $teknisi->id,
                'catatan' => 'Segera periksa dan perbaiki kursi di baris depan.',
            ]);

        $response->assertRedirect(route('admin.laporan.index'))
            ->assertSessionHas('banner_sukses');

        $this->actingAs($admin)
            ->get(route('admin.laporan.index'))
            ->assertOk()
            ->assertSee('Penugasan berhasil disimpan')
            ->assertSee("{$laporan->nomor_laporan} telah ditugaskan kepada {$teknisi->name}.");

        $laporan->refresh();
        $this->assertSame(StatusLaporan::Ditugaskan, $laporan->status);

        $penugasan = Penugasan::where('laporan_id', $laporan->id)->first();
        $this->assertNotNull($penugasan);
        $this->assertSame($teknisi->id, $penugasan->teknisi_id);
        $this->assertSame($admin->id, $penugasan->admin_id);
        $this->assertSame('Segera periksa dan perbaiki kursi di baris depan.', $penugasan->catatan);

        // Integrasi Modul 2 (Pelapor): timeline menampilkan status Ditugaskan beserta nama teknisi
        $this->actingAs($pelapor)
            ->get(route('pelapor.laporan.show', $laporan))
            ->assertOk()
            ->assertSee('Ditugaskan')
            ->assertSee($teknisi->name);
    }

    public function test_penugasan_gagal_jika_user_bukan_teknisi_atau_teknisi_nonaktif(): void
    {
        Storage::fake('public');
        $this->seed(Modul1DemoSeeder::class);

        $admin = User::where('email', 'admin.fmipa@uho.ac.id')->firstOrFail();
        $pelapor = User::where('email', 'nadia@uho.ac.id')->firstOrFail();
        $teknisiNonaktif = User::factory()->create([
            'role' => 'teknisi',
            'aktif' => false,
        ]);
        $laporan = Laporan::where('status', StatusLaporan::Diverifikasi->value)->firstOrFail();

        foreach ([$pelapor->id, $admin->id, $teknisiNonaktif->id] as $invalidId) {
            $this->actingAs($admin)
                ->from(route('admin.laporan.assign', $laporan))
                ->post(route('admin.laporan.assign.store', $laporan), [
                    'teknisi_id' => $invalidId,
                ])
                ->assertRedirect(route('admin.laporan.assign', $laporan))
                ->assertSessionHasErrors('teknisi_id');
        }

        $laporan->refresh();
        $this->assertSame(StatusLaporan::Diverifikasi, $laporan->status);
        $this->assertDatabaseMissing('penugasan', ['laporan_id' => $laporan->id]);
    }

    public function test_aturan_transisi_status_ketat_menolak_aksi_tidak_valid(): void
    {
        Storage::fake('public');
        $this->seed(Modul1DemoSeeder::class);

        $admin = User::where('email', 'admin.fmipa@uho.ac.id')->firstOrFail();
        $teknisi = User::where('email', 'andi@uho.ac.id')->firstOrFail();

        $laporanDiverifikasi = Laporan::where('status', StatusLaporan::Diverifikasi->value)->firstOrFail();
        $laporanMenunggu = Laporan::where('status', StatusLaporan::MenungguVerifikasi->value)->firstOrFail();
        $laporanDitugaskan = Laporan::where('status', StatusLaporan::Ditugaskan->value)->firstOrFail();

        // Tidak boleh approve/reject laporan yang sudah Diverifikasi
        $this->actingAs($admin)
            ->post(route('admin.laporan.approve', $laporanDiverifikasi))
            ->assertRedirect(route('admin.laporan.show', $laporanDiverifikasi))
            ->assertSessionHas('galat');

        $this->actingAs($admin)
            ->post(route('admin.laporan.reject', $laporanDiverifikasi), ['alasan_penolakan' => 'Alasan tidak sah'])
            ->assertRedirect(route('admin.laporan.show', $laporanDiverifikasi))
            ->assertSessionHas('galat');

        $this->assertSame(StatusLaporan::Diverifikasi, $laporanDiverifikasi->fresh()->status);

        // Tidak boleh menugaskan teknisi pada laporan Menunggu Verifikasi atau Ditugaskan
        $this->actingAs($admin)
            ->get(route('admin.laporan.assign', $laporanMenunggu))
            ->assertRedirect(route('admin.laporan.show', $laporanMenunggu))
            ->assertSessionHas('galat');

        $this->actingAs($admin)
            ->post(route('admin.laporan.assign.store', $laporanMenunggu), ['teknisi_id' => $teknisi->id])
            ->assertRedirect(route('admin.laporan.show', $laporanMenunggu))
            ->assertSessionHas('galat');

        $this->assertDatabaseMissing('penugasan', ['laporan_id' => $laporanMenunggu->id]);

        $this->actingAs($admin)
            ->post(route('admin.laporan.assign.store', $laporanDitugaskan), ['teknisi_id' => $teknisi->id])
            ->assertRedirect(route('admin.laporan.show', $laporanDitugaskan))
            ->assertSessionHas('galat');
    }
}
