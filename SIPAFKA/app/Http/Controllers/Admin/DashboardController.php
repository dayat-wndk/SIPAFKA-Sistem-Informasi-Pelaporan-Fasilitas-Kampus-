<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusLaporan;
use App\Http\Controllers\Controller;
use App\Models\Laporan;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $countsByStatus = Laporan::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $totalLaporan = (int) $countsByStatus->sum();
        $menungguVerifikasi = (int) ($countsByStatus[StatusLaporan::MenungguVerifikasi->value] ?? 0);
        $ditugaskan = (int) ($countsByStatus[StatusLaporan::Ditugaskan->value] ?? 0);
        $diproses = (int) ($countsByStatus[StatusLaporan::Diproses->value] ?? 0);
        $selesai = (int) ($countsByStatus[StatusLaporan::Selesai->value] ?? 0);

        $barColors = [
            StatusLaporan::MenungguVerifikasi->value => '#D97706',
            StatusLaporan::Diverifikasi->value => '#16A34A',
            StatusLaporan::Ditugaskan->value => '#7C3AED',
            StatusLaporan::Diproses->value => '#2563EB',
            StatusLaporan::Selesai->value => '#059669',
            StatusLaporan::Ditolak->value => '#DC2626',
        ];

        $distribusi = collect(StatusLaporan::cases())->map(function (StatusLaporan $status) use ($countsByStatus, $totalLaporan, $barColors) {
            $jumlah = (int) ($countsByStatus[$status->value] ?? 0);
            $persen = $totalLaporan > 0 ? round(($jumlah / $totalLaporan) * 100, 1) : 0;

            return [
                'status' => $status,
                'jumlah' => $jumlah,
                'persen' => $jumlah > 0 ? max($persen, 6) : 0,
                'warna' => $barColors[$status->value] ?? '#0F2D52',
            ];
        });

        $laporanTerbaru = Laporan::query()
            ->with(['pelapor', 'fasilitas'])
            ->latest('tanggal')
            ->latest('id')
            ->take(3)
            ->get();

        return view('admin.dashboard', compact(
            'totalLaporan',
            'menungguVerifikasi',
            'ditugaskan',
            'diproses',
            'selesai',
            'distribusi',
            'laporanTerbaru',
        ));
    }
}
