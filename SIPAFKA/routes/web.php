<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        return match (Auth::user()->role) {
            'pelapor' => redirect()->route('pelapor.dashboard'),
            'admin' => redirect('/admin/dashboard'),
            'teknisi' => redirect('/teknisi/dashboard'),
            default => redirect()->route('login'),
        };
    }

    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ROUTE MILIK HATMAN FIRDAYAT DIMULAI
Route::middleware(['auth', 'aktif', 'role:pelapor'])
    ->prefix('pelapor')
    ->name('pelapor.')
    ->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Pelapor\DashboardController::class, 'index'])
            ->name('dashboard');
        Route::get('/laporan', [\App\Http\Controllers\Pelapor\LaporanController::class, 'index'])
            ->name('laporan.index');
        Route::get('/laporan/buat', [\App\Http\Controllers\Pelapor\LaporanController::class, 'create'])
            ->name('laporan.create');
        Route::post('/laporan', [\App\Http\Controllers\Pelapor\LaporanController::class, 'store'])
            ->name('laporan.store');
        Route::get('/laporan/{laporan}/berhasil', [\App\Http\Controllers\Pelapor\LaporanController::class, 'sukses'])
            ->name('laporan.sukses');
        Route::get('/laporan/{laporan}', [\App\Http\Controllers\Pelapor\LaporanController::class, 'show'])
            ->name('laporan.show');
    });
// ROUTE MILIK HATMAN FIRDAYAT BERAKHIR

// ROUTE MILIK MIRAWATI DIMULAI
Route::middleware(['auth', 'aktif', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/laporan', [\App\Http\Controllers\Admin\VerifikasiLaporanController::class, 'index'])
            ->name('laporan.index');
        Route::get('/laporan/{laporan}', [\App\Http\Controllers\Admin\VerifikasiLaporanController::class, 'show'])
            ->name('laporan.show');
        Route::post('/laporan/{laporan}/verifikasi', [\App\Http\Controllers\Admin\VerifikasiLaporanController::class, 'approve'])
            ->name('laporan.approve');
        Route::post('/laporan/{laporan}/tolak', [\App\Http\Controllers\Admin\VerifikasiLaporanController::class, 'reject'])
            ->name('laporan.reject');
        Route::get('/laporan/{laporan}/ditolak', [\App\Http\Controllers\Admin\VerifikasiLaporanController::class, 'rejectedView'])
            ->name('laporan.rejected');
        Route::get('/laporan/{laporan}/tugaskan', [\App\Http\Controllers\Admin\PenugasanController::class, 'create'])
            ->name('laporan.assign');
        Route::post('/laporan/{laporan}/tugaskan', [\App\Http\Controllers\Admin\PenugasanController::class, 'store'])
            ->name('laporan.assign.store');
    });
// ROUTE MILIK MIRAWATI BERAKHIR

// ROUTE MILIK NUR RUSYDA DIMULAI
Route::middleware(['auth', 'aktif', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/pengguna', [\App\Http\Controllers\Admin\UserController::class, 'index'])
            ->name('users.index');
        Route::post('/pengguna', [\App\Http\Controllers\Admin\UserController::class, 'store'])
            ->name('users.store');
        Route::get('/pengguna/{user}/edit', [\App\Http\Controllers\Admin\UserController::class, 'edit'])
            ->name('users.edit');
        Route::put('/pengguna/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])
            ->name('users.update');

        Route::get('/fasilitas', [\App\Http\Controllers\Admin\FasilitasController::class, 'index'])
            ->name('fasilitas.index');
        Route::post('/fasilitas', [\App\Http\Controllers\Admin\FasilitasController::class, 'store'])
            ->name('fasilitas.store');
        Route::get('/fasilitas/{fasilitas}/edit', [\App\Http\Controllers\Admin\FasilitasController::class, 'edit'])
            ->name('fasilitas.edit');
        Route::put('/fasilitas/{fasilitas}', [\App\Http\Controllers\Admin\FasilitasController::class, 'update'])
            ->name('fasilitas.update');
    });
// ROUTE MILIK NUR RUSYDA BERAKHIR

// ROUTE MILIK MUH IMAN DIMULAI
Route::middleware(['auth', 'aktif', 'role:teknisi'])
    ->prefix('teknisi')
    ->name('teknisi.')
    ->group(function () {
        Route::get('/dashboard', fn () => redirect()->route('teknisi.tugas.index'))
            ->name('dashboard');
        Route::get('/tugas', [\App\Http\Controllers\Teknisi\TugasController::class, 'index'])
            ->name('tugas.index');
        Route::get('/tugas/{laporan}', [\App\Http\Controllers\Teknisi\TugasController::class, 'show'])
            ->name('tugas.show');
        Route::post('/tugas/{laporan}/mulai', [\App\Http\Controllers\Teknisi\TugasController::class, 'startRepair'])
            ->name('tugas.start');
        Route::get('/tugas/{laporan}/perbarui', [\App\Http\Controllers\Teknisi\TugasController::class, 'edit'])
            ->name('tugas.edit');
        Route::match(['put', 'patch'], '/tugas/{laporan}', [\App\Http\Controllers\Teknisi\TugasController::class, 'update'])
            ->name('tugas.update');
        Route::get('/tugas/{laporan}/selesai', [\App\Http\Controllers\Teknisi\TugasController::class, 'completed'])
            ->name('tugas.completed');
    });
// ROUTE MILIK MUH IMAN BERAKHIR
