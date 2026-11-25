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
