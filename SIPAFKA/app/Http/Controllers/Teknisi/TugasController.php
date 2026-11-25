<?php

namespace App\Http\Controllers\Teknisi;

use App\Enums\StatusLaporan;
use App\Http\Controllers\Controller;
use App\Http\Requests\Teknisi\UpdateProgresRequest;
use App\Models\Laporan;
use App\Models\Perbaikan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TugasController extends Controller
{
    private function authorizeTask(Laporan $laporan): void
    {
        $laporan->loadMissing('penugasan');

        if (! $laporan->penugasan || (int) $laporan->penugasan->teknisi_id !== (int) Auth::id()) {
            abort(403, 'Anda tidak memiliki izin untuk mengakses tugas perbaikan ini.');
        }
    }

    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', $request->query('cari', '')));
        $statusFilter = trim((string) $request->query('status', ''));

        $allowedStatuses = [
            StatusLaporan::Ditugaskan,
            StatusLaporan::Diproses,
            StatusLaporan::Selesai,
        ];
        $allowedStatusValues = array_map(
            fn (StatusLaporan $status): string => $status->value,
            $allowedStatuses
        );

        $query = Laporan::query()
            ->whereHas('penugasan', fn ($sub) => $sub->where('teknisi_id', Auth::id()))
            ->with(['fasilitas', 'penugasan']);

        if ($q !== '') {
            $idDariNomor = null;
            if (preg_match('/^(?:SIP-)?0*(\d+)$/i', $q, $matches)) {
                $idDariNomor = (int) $matches[1];
            }

            $query->where(function ($sub) use ($q, $idDariNomor): void {
                $sub->where('judul', 'like', '%' . $q . '%')
                    ->orWhereHas('fasilitas', function ($fasilitasQuery) use ($q): void {
                        $fasilitasQuery->where('nama', 'like', '%' . $q . '%')
                            ->orWhere('ruang', 'like', '%' . $q . '%')
                            ->orWhere('gedung', 'like', '%' . $q . '%');
                    });

                if ($idDariNomor !== null) {
                    $sub->orWhere('id', $idDariNomor);
                }
            });
        }

        if ($statusFilter !== '' && in_array($statusFilter, $allowedStatusValues, true)) {
            $query->where('status', $statusFilter);
        }

        $tugasList = $query
            ->latest('tanggal')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('teknisi.tugas.index', compact(
            'tugasList',
            'allowedStatuses',
            'q',
            'statusFilter',
        ));
    }

    public function show(Laporan $laporan): View
    {
        $this->authorizeTask($laporan);

        $laporan->load([
            'fasilitas',
            'pelapor',
            'admin',
            'penugasan.teknisi',
            'penugasan.admin',
            'penugasan.perbaikan' => fn ($query) => $query->orderByDesc('tanggal')->orderByDesc('id'),
        ]);

        $ukuranFoto = null;
        if ($laporan->foto && Storage::disk('public')->exists($laporan->foto)) {
            $bytes = Storage::disk('public')->size($laporan->foto);
            $ukuranFoto = $bytes >= 1024 * 1024
                ? str_replace('.', ',', number_format($bytes / (1024 * 1024), 1)) . ' MB'
                : max(1, (int) round($bytes / 1024)) . ' KB';
        }

        return view('teknisi.tugas.show', compact('laporan', 'ukuranFoto'));
    }

    public function startRepair(Laporan $laporan): RedirectResponse
    {
        $this->authorizeTask($laporan);

        if ($laporan->status === StatusLaporan::Selesai) {
            return redirect()
                ->route('teknisi.tugas.completed', $laporan)
                ->with('galat', 'Tugas yang sudah selesai tidak dapat diubah kembali.');
        }

        if ($laporan->status === StatusLaporan::Diproses) {
            return redirect()->route('teknisi.tugas.edit', $laporan);
        }

        if ($laporan->status !== StatusLaporan::Ditugaskan) {
            abort(403, 'Tugas ini tidak berada pada status Ditugaskan.');
        }

        try {
            DB::transaction(function () use ($laporan): void {
                $locked = Laporan::query()
                    ->with('penugasan')
                    ->whereKey($laporan->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if (! $locked->penugasan || (int) $locked->penugasan->teknisi_id !== (int) Auth::id()) {
                    abort(403, 'Anda tidak memiliki izin untuk mengakses tugas perbaikan ini.');
                }

                if ($locked->status !== StatusLaporan::Ditugaskan) {
                    throw new \RuntimeException('Status tugas telah berubah dan tidak lagi berstatus Ditugaskan.');
                }

                $locked->update([
                    'status' => StatusLaporan::Diproses->value,
                ]);

                Perbaikan::create([
                    'penugasan_id' => $locked->penugasan->id,
                    'tanggal' => now(),
                    'catatan_progres' => 'Teknisi memulai perbaikan.',
                    'hasil' => null,
                ]);
            });
        } catch (\RuntimeException $e) {
            return redirect()
                ->route('teknisi.tugas.show', $laporan)
                ->with('galat', $e->getMessage());
        }

        return redirect()
            ->route('teknisi.tugas.edit', $laporan)
            ->with('sukses', 'Perbaikan dimulai. Silakan perbarui status dan catatan pekerjaan.');
    }

    public function edit(Laporan $laporan): View|RedirectResponse
    {
        $this->authorizeTask($laporan);

        if ($laporan->status === StatusLaporan::Selesai) {
            return redirect()
                ->route('teknisi.tugas.completed', $laporan)
                ->with('galat', 'Tugas yang sudah selesai tidak dapat diubah kembali.');
        }

        if ($laporan->status === StatusLaporan::Ditugaskan) {
            return redirect()
                ->route('teknisi.tugas.show', $laporan)
                ->with('galat', 'Klik tombol Mulai perbaikan terlebih dahulu sebelum memperbarui progres.');
        }

        if ($laporan->status !== StatusLaporan::Diproses) {
            abort(403, 'Status tugas tidak valid untuk diperbarui.');
        }

        $laporan->load([
            'fasilitas',
            'penugasan.teknisi',
            'penugasan.admin',
            'penugasan.perbaikan' => fn ($query) => $query->orderByDesc('tanggal')->orderByDesc('id'),
        ]);

        $riwayatPerbaikan = $laporan->penugasan?->perbaikan ?? collect();

        return view('teknisi.tugas.edit', compact('laporan', 'riwayatPerbaikan'));
    }

    public function update(UpdateProgresRequest $request, Laporan $laporan): RedirectResponse
    {
        $this->authorizeTask($laporan);

        if ($laporan->status === StatusLaporan::Selesai) {
            return redirect()
                ->route('teknisi.tugas.completed', $laporan)
                ->with('galat', 'Tugas yang sudah selesai tidak dapat diubah mundur lagi.');
        }

        if ($laporan->status !== StatusLaporan::Diproses) {
            return redirect()
                ->route('teknisi.tugas.show', $laporan)
                ->with('galat', 'Tugas harus berstatus Diproses sebelum progres diperbarui.');
        }

        $statusBaru = $request->validated('status');
        $catatan = trim((string) $request->validated('catatan'));

        try {
            DB::transaction(function () use ($laporan, $statusBaru, $catatan): void {
                $locked = Laporan::query()
                    ->with('penugasan')
                    ->whereKey($laporan->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if (! $locked->penugasan || (int) $locked->penugasan->teknisi_id !== (int) Auth::id()) {
                    abort(403, 'Anda tidak memiliki izin untuk mengakses tugas perbaikan ini.');
                }

                if ($locked->status === StatusLaporan::Selesai) {
                    throw new \RuntimeException('Tugas yang sudah selesai tidak dapat diubah mundur lagi.');
                }

                if ($locked->status !== StatusLaporan::Diproses) {
                    throw new \RuntimeException('Status tugas harus Diproses sebelum diperbarui.');
                }

                $locked->update([
                    'status' => $statusBaru,
                ]);

                Perbaikan::create([
                    'penugasan_id' => $locked->penugasan->id,
                    'tanggal' => now(),
                    'catatan_progres' => $catatan,
                    'hasil' => $statusBaru === StatusLaporan::Selesai->value ? $catatan : null,
                ]);
            });
        } catch (\RuntimeException $e) {
            return redirect()
                ->route('teknisi.tugas.show', $laporan)
                ->with('galat', $e->getMessage());
        }

        if ($statusBaru === StatusLaporan::Selesai->value) {
            return redirect()
                ->route('teknisi.tugas.completed', $laporan)
                ->with('sukses', 'Perbaikan berhasil diselesaikan.');
        }

        return redirect()
            ->route('teknisi.tugas.edit', $laporan)
            ->with('sukses', 'Pembaruan progres perbaikan berhasil disimpan.');
    }

    public function completed(Laporan $laporan): View|RedirectResponse
    {
        $this->authorizeTask($laporan);

        if ($laporan->status !== StatusLaporan::Selesai) {
            return redirect()
                ->route('teknisi.tugas.show', $laporan)
                ->with('galat', 'Perbaikan untuk laporan ini belum diselesaikan.');
        }

        $laporan->load([
            'fasilitas',
            'penugasan.teknisi',
            'penugasan.perbaikan' => fn ($query) => $query->orderByDesc('tanggal')->orderByDesc('id'),
        ]);

        $riwayatPerbaikan = $laporan->penugasan?->perbaikan ?? collect();
        $perbaikanSelesai = $riwayatPerbaikan->first(fn ($item): bool => filled($item->hasil))
            ?? $riwayatPerbaikan->first();

        return view('teknisi.tugas.completed', compact('laporan', 'perbaikanSelesai'));
    }
}
