<?php

namespace App\Http\Controllers\Pelapor;

use App\Enums\StatusLaporan;
use App\Http\Controllers\Controller;
use App\Http\Requests\Pelapor\StoreLaporanRequest;
use App\Models\Fasilitas;
use App\Models\Laporan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class LaporanController extends Controller
{
    public function index(Request $request): View
    {
        $cari = trim((string) $request->query('cari', $request->query('q', '')));
        $statusFilter = trim((string) $request->query('status', ''));

        $query = Auth::user()
            ->laporanDibuat()
            ->with('fasilitas');

        if ($cari !== '') {
            $idDariNomor = null;
            if (preg_match('/^(?:SIP-)?0*(\d+)$/i', $cari, $matches)) {
                $idDariNomor = (int) $matches[1];
            }

            $query->where(function ($q) use ($cari, $idDariNomor) {
                $q->where('judul', 'like', '%' . $cari . '%');

                if ($idDariNomor !== null) {
                    $q->orWhere('id', $idDariNomor);
                }
            });
        }

        if ($statusFilter !== '' && in_array($statusFilter, StatusLaporan::values(), true)) {
            $query->where('status', $statusFilter);
        }

        $laporanList = $query
            ->latest('tanggal')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $daftarStatus = StatusLaporan::cases();

        return view('pelapor.laporan.index', compact(
            'laporanList',
            'daftarStatus',
            'cari',
            'statusFilter',
        ));
    }

    public function create(): View
    {
        $fasilitas = Fasilitas::query()
            ->orderBy('nama')
            ->orderBy('ruang')
            ->get(['id', 'nama', 'gedung', 'ruang']);

        $namaFasilitasList = $fasilitas->pluck('nama')->unique()->values();

        $fasilitasJson = $fasilitas->map(fn (Fasilitas $item) => [
            'id' => $item->id,
            'nama' => $item->nama,
            'gedung' => $item->gedung,
            'ruang' => $item->ruang,
            'label' => $item->ruang . ', ' . $item->gedung,
        ])->values();

        $oldFasilitasId = old('fasilitas_id');
        $oldFasilitasNama = old('fasilitas_nama');
        if (! $oldFasilitasNama && $oldFasilitasId) {
            $oldFasilitasNama = $fasilitas->firstWhere('id', (int) $oldFasilitasId)?->nama;
        }

        return view('pelapor.laporan.create', compact(
            'namaFasilitasList',
            'fasilitasJson',
            'oldFasilitasId',
            'oldFasilitasNama',
        ));
    }

    public function store(StoreLaporanRequest $request): RedirectResponse
    {
        $fotoPath = $request->file('foto')->store('laporan-foto', 'public');

        $laporan = Laporan::create([
            'pelapor_id' => Auth::id(),
            'fasilitas_id' => $request->validated('fasilitas_id'),
            'judul' => $request->validated('judul'),
            'deskripsi' => $request->validated('deskripsi'),
            'foto' => $fotoPath,
            'tanggal' => now()->toDateString(),
            'status' => StatusLaporan::MenungguVerifikasi->value,
        ]);

        return redirect()->route('pelapor.laporan.sukses', $laporan);
    }

    public function show(Laporan $laporan): View
    {
        if ((int) $laporan->pelapor_id !== (int) Auth::id()) {
            abort(404);
        }

        $laporan->load([
            'pelapor',
            'fasilitas',
            'admin',
            'penugasan.teknisi',
            'penugasan.admin',
            'penugasan.perbaikan' => fn ($query) => $query->orderBy('tanggal')->orderBy('id'),
        ]);

        $daftarPerbaikan = $laporan->penugasan?->perbaikan ?? collect();
        $perbaikanPertama = $daftarPerbaikan->first();
        $perbaikanTerakhir = $daftarPerbaikan->last();
        $perbaikanSelesai = $daftarPerbaikan->last(fn ($item) => filled($item->hasil));

        $ukuranFoto = null;
        if ($laporan->foto && Storage::disk('public')->exists($laporan->foto)) {
            $bytes = Storage::disk('public')->size($laporan->foto);
            $ukuranFoto = $bytes >= 1024 * 1024
                ? str_replace('.', ',', number_format($bytes / (1024 * 1024), 1)) . ' MB'
                : max(1, (int) round($bytes / 1024)) . ' KB';
        }

        return view('pelapor.laporan.show', compact(
            'laporan',
            'perbaikanPertama',
            'perbaikanTerakhir',
            'perbaikanSelesai',
            'ukuranFoto',
        ));
    }

    public function sukses(Laporan $laporan): View
    {
        if ((int) $laporan->pelapor_id !== (int) Auth::id()) {
            abort(404);
        }

        $laporan->load('fasilitas');

        return view('pelapor.laporan.sukses', compact('laporan'));
    }
}
