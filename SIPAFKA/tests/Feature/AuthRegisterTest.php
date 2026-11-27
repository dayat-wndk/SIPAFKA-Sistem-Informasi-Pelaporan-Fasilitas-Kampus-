<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthRegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_login_menampilkan_tautan_daftar_dan_halaman_register_dapat_diakses(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee(route('register'), false)
            ->assertSee('Daftar sekarang');

        $this->get(route('register'))
            ->assertOk()
            ->assertSee('Buat akun baru')
            ->assertSee('Konfirmasi kata sandi');
    }

    public function test_pengguna_baru_dapat_mendaftar_sebagai_pelapor_dan_langsung_masuk(): void
    {
        $response = $this->post(route('register.post'), [
            'name' => 'Mahasiswa Baru',
            'email' => 'Mahasiswa.Baru@uho.ac.id',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
            // Upaya manipulasi peran harus diabaikan.
            'role' => 'admin',
        ]);

        $response->assertRedirect(route('pelapor.dashboard'));

        $user = User::where('email', 'mahasiswa.baru@uho.ac.id')->first();
        $this->assertNotNull($user);
        $this->assertSame('Mahasiswa Baru', $user->name);
        $this->assertSame('pelapor', $user->role);
        $this->assertTrue($user->aktif);
        $this->assertAuthenticatedAs($user);

        $this->get(route('pelapor.dashboard'))
            ->assertOk()
            ->assertSee('Mahasiswa Baru');
    }

    public function test_pendaftaran_menolak_email_duplikat_atau_konfirmasi_password_tidak_sesuai(): void
    {
        $this->seed(UserSeeder::class);

        $this->from(route('register'))
            ->post(route('register.post'), [
                'name' => 'Duplikat',
                'email' => 'nadia@uho.ac.id',
                'password' => '1234567',
                'password_confirmation' => 'berbeda',
            ])
            ->assertRedirect(route('register'))
            ->assertSessionHasErrors(['email', 'password']);

        $this->assertGuest();
    }

    public function test_pengguna_yang_sudah_login_dialihkan_dari_halaman_register(): void
    {
        $this->seed(UserSeeder::class);
        $pelapor = User::where('email', 'nadia@uho.ac.id')->firstOrFail();

        $this->actingAs($pelapor)
            ->get(route('register'))
            ->assertRedirect('/');
    }
}
