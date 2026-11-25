<?php

namespace App\Http\Controllers\Pelapor;

use App\Enums\StatusLaporan;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $totalLaporan = $user->laporanDibuat()->count();

        $menungguVerifikasi = $user->laporanDibuat()
            ->where('status', StatusLaporan::MenungguVerifikasi->value)
            ->count();

        $dalamPenanganan = $user->laporanDibuat()
            ->whereIn('status', [
                StatusLaporan::Diverifikasi->value,
                StatusLaporan::Ditugaskan->value,
                StatusLaporan::Diproses->value,
            ])
            ->count();

        $selesai = $user->laporanDibuat()
            ->where('status', StatusLaporan::Selesai->value)
            ->count();

        $laporanTerbaru = $user->laporanDibuat()
            ->with('fasilitas')
            ->latest('tanggal')
            ->latest('id')
            ->take(3)
            ->get();

        return view('pelapor.dashboard', compact(
            'totalLaporan',
            'menungguVerifikasi',
            'dalamPenanganan',
            'selesai',
            'laporanTerbaru',
        ));
    }
}
