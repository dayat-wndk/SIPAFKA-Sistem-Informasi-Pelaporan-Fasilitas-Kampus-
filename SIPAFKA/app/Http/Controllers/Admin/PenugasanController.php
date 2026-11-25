<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusLaporan;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PenugasanTeknisiRequest;
use App\Models\Laporan;
use App\Models\Penugasan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PenugasanController extends Controller
{
    public function create(Laporan $laporan): View|RedirectResponse
    {
        if ($laporan->status !== StatusLaporan::Diverifikasi) {
            return redirect()
                ->route('admin.laporan.show', $laporan)
                ->with('galat', "Penugasan teknisi hanya dapat dilakukan pada laporan berstatus Diverifikasi.");
        }

        $laporan->load(['pelapor', 'fasilitas', 'admin']);

        $teknisiList = User::query()
            ->where('role', 'teknisi')
            ->where('aktif', true)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return view('admin.laporan.assign', compact('laporan', 'teknisiList'));
    }

    public function store(PenugasanTeknisiRequest $request, Laporan $laporan): RedirectResponse
    {
        if ($laporan->status !== StatusLaporan::Diverifikasi) {
            return redirect()
                ->route('admin.laporan.show', $laporan)
                ->with('galat', "Penugasan gagal: laporan {$laporan->nomor_laporan} tidak berstatus Diverifikasi.");
        }

        $teknisi = User::query()
            ->where('role', 'teknisi')
            ->where('aktif', true)
            ->findOrFail($request->validated('teknisi_id'));

        try {
            DB::transaction(function () use ($laporan, $teknisi, $request): void {
                $locked = Laporan::query()->whereKey($laporan->id)->lockForUpdate()->firstOrFail();

                if ($locked->status !== StatusLaporan::Diverifikasi || $locked->penugasan()->exists()) {
                    throw new \RuntimeException('Laporan sudah ditugaskan atau statusnya telah berubah.');
                }

                Penugasan::create([
                    'laporan_id' => $locked->id,
                    'teknisi_id' => $teknisi->id,
                    'admin_id' => Auth::id(),
                    'tanggal_tugas' => now(),
                    'catatan' => $request->validated('catatan'),
                ]);

                $locked->update([
                    'status' => StatusLaporan::Ditugaskan->value,
                ]);
            });
        } catch (\RuntimeException $e) {
            return redirect()
                ->route('admin.laporan.show', $laporan)
                ->with('galat', $e->getMessage());
        }

        return redirect()
            ->route('admin.laporan.index')
            ->with('banner_sukses', [
                'judul' => 'Penugasan berhasil disimpan',
                'pesan' => "{$laporan->nomor_laporan} telah ditugaskan kepada {$teknisi->name}.",
            ]);
    }
}
