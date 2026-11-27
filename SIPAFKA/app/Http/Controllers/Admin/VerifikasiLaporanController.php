<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusLaporan;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\VerifikasiLaporanRequest;
use App\Models\Fasilitas;
use App\Models\Laporan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class VerifikasiLaporanController extends Controller
{
    public function index(Request $request): View
    {
        $cari = trim((string) $request->query('cari', ''));
        $statusFilter = trim((string) $request->query('status', ''));
        $fasilitasFilter = trim((string) $request->query('fasilitas_id', ''));

        $query = Laporan::query()
            ->with(['pelapor', 'fasilitas', 'admin', 'penugasan.teknisi']);

        if ($cari !== '') {
            $idDariNomor = null;
            if (preg_match('/^(?:SIP-)?0*(\d+)$/i', $cari, $matches)) {
                $idDariNomor = (int) $matches[1];
            }

            $query->where(function ($q) use ($cari, $idDariNomor) {
                $q->where('judul', 'like', '%' . $cari . '%')
                    ->orWhereHas('pelapor', fn ($pelaporQuery) => $pelaporQuery->where('name', 'like', '%' . $cari . '%'));

                if ($idDariNomor !== null) {
                    $q->orWhere('id', $idDariNomor);
                }
            });
        }

        if ($statusFilter !== '' && in_array($statusFilter, StatusLaporan::values(), true)) {
            $query->where('status', $statusFilter);
        }

        if ($fasilitasFilter !== '' && ctype_digit($fasilitasFilter)) {
            $query->where('fasilitas_id', (int) $fasilitasFilter);
        }

        $laporanList = $query
            ->latest('tanggal')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $daftarStatus = StatusLaporan::cases();
        $daftarFasilitas = Fasilitas::query()
            ->orderBy('nama')
            ->orderBy('ruang')
            ->get(['id', 'nama', 'gedung', 'ruang']);

        return view('admin.laporan.index', compact(
            'laporanList',
            'daftarStatus',
            'daftarFasilitas',
            'cari',
            'statusFilter',
            'fasilitasFilter',
        ));
    }

    public function show(Laporan $laporan): View|RedirectResponse
    {
        if ($laporan->status === StatusLaporan::Ditolak) {
            return redirect()->route('admin.laporan.rejected', $laporan);
        }

        $laporan->load([
            'pelapor',
            'fasilitas',
            'admin',
            'penugasan.teknisi',
            'penugasan.admin',
        ]);

        return view('admin.laporan.verify', compact('laporan'));
    }

    public function approve(VerifikasiLaporanRequest $request, Laporan $laporan): RedirectResponse
    {
        if ($laporan->status !== StatusLaporan::MenungguVerifikasi) {
            return redirect()
                ->route('admin.laporan.show', $laporan)
                ->with('galat', "Laporan {$laporan->nomor_laporan} hanya dapat diverifikasi ketika berstatus Menunggu Verifikasi.");
        }

        try {
            DB::transaction(function () use ($laporan): void {
                $locked = Laporan::query()->whereKey($laporan->id)->lockForUpdate()->firstOrFail();

                if ($locked->status !== StatusLaporan::MenungguVerifikasi) {
                    throw new \RuntimeException('Status laporan sudah berubah sebelum verifikasi disimpan.');
                }

                $locked->update([
                    'status' => StatusLaporan::Diverifikasi->value,
                    'admin_id' => Auth::id(),
                    'diverifikasi_at' => now(),
                    'alasan_penolakan' => null,
                ]);
            });
        } catch (\RuntimeException $e) {
            return redirect()
                ->route('admin.laporan.show', $laporan)
                ->with('galat', $e->getMessage());
        }

        return redirect()
            ->route('admin.laporan.index')
            ->with('sukses', "Laporan {$laporan->nomor_laporan} berhasil diverifikasi dan siap ditugaskan.");
    }

    public function reject(VerifikasiLaporanRequest $request, Laporan $laporan): RedirectResponse
    {
        if ($laporan->status !== StatusLaporan::MenungguVerifikasi) {
            return redirect()
                ->route('admin.laporan.show', $laporan)
                ->with('galat', "Laporan {$laporan->nomor_laporan} hanya dapat ditolak ketika berstatus Menunggu Verifikasi.");
        }

        $alasanPenolakan = $request->validated('alasan_penolakan');

        try {
            DB::transaction(function () use ($laporan, $alasanPenolakan): void {
                $locked = Laporan::query()->whereKey($laporan->id)->lockForUpdate()->firstOrFail();

                if ($locked->status !== StatusLaporan::MenungguVerifikasi) {
                    throw new \RuntimeException('Status laporan sudah berubah sebelum penolakan disimpan.');
                }

                $locked->update([
                    'status' => StatusLaporan::Ditolak->value,
                    'admin_id' => Auth::id(),
                    'diverifikasi_at' => now(),
                    'alasan_penolakan' => $alasanPenolakan,
                ]);
            });
        } catch (\RuntimeException $e) {
            return redirect()
                ->route('admin.laporan.show', $laporan)
                ->with('galat', $e->getMessage());
        }

        return redirect()
            ->route('admin.laporan.rejected', $laporan)
            ->with('sukses', "Laporan {$laporan->nomor_laporan} telah ditolak.");
    }

    public function rejectedView(Laporan $laporan): View|RedirectResponse
    {
        if ($laporan->status !== StatusLaporan::Ditolak) {
            return redirect()->route('admin.laporan.show', $laporan);
        }

        $laporan->load(['pelapor', 'fasilitas', 'admin']);

        return view('admin.laporan.rejected', compact('laporan'));
    }
}
