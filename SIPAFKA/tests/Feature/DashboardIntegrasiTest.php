<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\FasilitasSeeder;
use Database\Seeders\Modul1DemoSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DashboardIntegrasiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([UserSeeder::class, FasilitasSeeder::class]);
    }

    public function test_admin_login_dan_dashboard_serta_sidebar_dan_modul_4_berjalan(): void
    {
        Storage::fake('public');
        $this->seed(Modul1DemoSeeder::class);

        $admin = User::where('email', 'admin.fmipa@uho.ac.id')->firstOrFail();
        $pelapor = User::where('email', 'nadia@uho.ac.id')->firstOrFail();
        $teknisi = User::where('email', 'andi@uho.ac.id')->firstOrFail();

        $this->post(route('login.post'), [
            'email' => $admin->email,
            'password' => 'password123',
        ])->assertRedirect('/admin/dashboard');

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertViewHas('totalLaporan', 6)
            ->assertViewHas('menungguVerifikasi', 1)
            ->assertViewHas('ditugaskan', 1)
            ->assertViewHas('diproses', 1)
            ->assertViewHas('selesai', 1)
            ->assertSee('Dashboard admin')
            ->assertSee('Distribusi status laporan')
            ->assertSee('Laporan masuk')
            ->assertSee('Kelola pengguna')
            ->assertSee('Kelola fasilitas');

        // Route Modul 4 berfungsi untuk admin
        $this->actingAs($admin)->get(route('admin.users.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.fasilitas.index'))->assertOk();

        // Role lain ditolak 403
        $this->actingAs($pelapor)->get(route('admin.dashboard'))->assertForbidden();
        $this->actingAs($teknisi)->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_teknisi_login_dan_dashboard_menampilkan_tugas_milik_sendiri_dan_sidebar(): void
    {
        Storage::fake('public');
        $this->seed(Modul1DemoSeeder::class);

        $andi = User::where('email', 'andi@uho.ac.id')->firstOrFail();
        $siti = User::where('email', 'siti@uho.ac.id')->firstOrFail();
        $pelapor = User::where('email', 'nadia@uho.ac.id')->firstOrFail();
        $admin = User::where('email', 'admin.fmipa@uho.ac.id')->firstOrFail();

        $this->post(route('login.post'), [
            'email' => $andi->email,
            'password' => 'password123',
        ])->assertRedirect('/teknisi/dashboard');

        $this->actingAs($andi)
            ->get(route('teknisi.dashboard'))
            ->assertOk()
            ->assertViewHas('totalTugas', 2)
            ->assertViewHas('ditugaskan', 1)
            ->assertViewHas('diproses', 1)
            ->assertViewHas('selesai', 0)
            ->assertSee('Selamat bekerja, Andi.')
            ->assertSee('Daftar tugas')
            ->assertSee('AC ruang kuliah tidak dingin')
            ->assertDontSee('Kran air bocor');

        $this->actingAs($siti)
            ->get(route('teknisi.dashboard'))
            ->assertOk()
            ->assertViewHas('totalTugas', 1)
            ->assertViewHas('selesai', 1)
            ->assertSee('Kran air bocor')
            ->assertDontSee('AC ruang kuliah tidak dingin');

        $this->actingAs($pelapor)->get(route('teknisi.dashboard'))->assertForbidden();
        $this->actingAs($admin)->get(route('teknisi.dashboard'))->assertForbidden();
    }
}
