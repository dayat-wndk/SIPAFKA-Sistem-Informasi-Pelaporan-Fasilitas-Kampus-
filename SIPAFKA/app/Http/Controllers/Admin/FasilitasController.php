<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreFasilitasRequest;
use App\Http\Requests\Admin\UpdateFasilitasRequest;
use App\Models\Fasilitas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FasilitasController extends Controller
{
    public function index(Request $request): View
    {
        $cari = trim((string) $request->query('cari', ''));

        $query = Fasilitas::query();

        if ($cari !== '') {
            $idDariKode = null;
            if (preg_match('/^(?:F-)?0*(\d+)$/i', $cari, $matches)) {
                $idDariKode = (int) $matches[1];
            }

            $query->where(function ($q) use ($cari, $idDariKode) {
                $q->where('nama', 'like', '%' . $cari . '%')
                    ->orWhere('ruang', 'like', '%' . $cari . '%')
                    ->orWhere('gedung', 'like', '%' . $cari . '%')
                    ->orWhere('keterangan', 'like', '%' . $cari . '%');

                if ($idDariKode !== null) {
                    $q->orWhere('id', $idDariKode);
                }
            });
        }

        $fasilitasList = $query
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();

        $kodeBerikutnya = $this->generateKodeFasilitas();

        return view('admin.fasilitas.index', compact('fasilitasList', 'cari', 'kodeBerikutnya'));
    }

    public function store(StoreFasilitasRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $fasilitas = Fasilitas::create([
            'nama' => $validated['nama'],
            'gedung' => $validated['gedung'] ?? 'Gedung FMIPA',
            'ruang' => $validated['lokasi'],
            'keterangan' => $validated['keterangan'] ?? null,
        ]);

        return redirect()
            ->route('admin.fasilitas.index')
            ->with('sukses', 'Fasilitas ' . $fasilitas->kode . ' berhasil ditambahkan.');
    }

    public function edit(Fasilitas $fasilitas): View
    {
        return view('admin.fasilitas.edit', compact('fasilitas'));
    }

    public function update(UpdateFasilitasRequest $request, Fasilitas $fasilitas): RedirectResponse
    {
        $validated = $request->validated();

        $fasilitas->update([
            'nama' => $validated['nama'],
            'gedung' => $validated['gedung'] ?? $fasilitas->gedung,
            'ruang' => $validated['lokasi'],
            'keterangan' => $validated['keterangan'] ?? null,
        ]);

        return redirect()
            ->route('admin.fasilitas.index')
            ->with('sukses', 'Data fasilitas ' . $fasilitas->kode . ' berhasil diperbarui.');
    }

    /**
     * Menghasilkan pratinjau kode fasilitas berurutan berikutnya (F-001, F-002, dst.).
     */
    private function generateKodeFasilitas(): string
    {
        $lastId = (int) (Fasilitas::query()->max('id') ?? 0);

        return 'F-' . str_pad((string) ($lastId + 1), 3, '0', STR_PAD_LEFT);
    }
}
