<?php

namespace App\Http\Controllers\Teknisi;

use App\Enums\StatusLaporan;
use App\Http\Controllers\Controller;
use App\Models\Laporan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $teknisiId = Auth::id();

        $baseQuery = Laporan::query()
            ->whereHas('penugasan', fn ($query) => $query->where('teknisi_id', $teknisiId));

        $countsByStatus = (clone $baseQuery)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $ditugaskan = (int) ($countsByStatus[StatusLaporan::Ditugaskan->value] ?? 0);
        $diproses = (int) ($countsByStatus[StatusLaporan::Diproses->value] ?? 0);
        $selesai = (int) ($countsByStatus[StatusLaporan::Selesai->value] ?? 0);
        $totalTugas = $ditugaskan + $diproses + $selesai;

        $tugasTerbaru = (clone $baseQuery)
            ->with(['fasilitas', 'penugasan'])
            ->whereIn('status', [
                StatusLaporan::Ditugaskan->value,
                StatusLaporan::Diproses->value,
                StatusLaporan::Selesai->value,
            ])
            ->latest('tanggal')
            ->latest('id')
            ->take(3)
            ->get();

        $namaDepan = Str::before(Auth::user()->name, ' ') ?: Auth::user()->name;

        return view('teknisi.dashboard', compact(
            'totalTugas',
            'ditugaskan',
            'diproses',
            'selesai',
            'tugasTerbaru',
            'namaDepan',
        ));
    }
}
