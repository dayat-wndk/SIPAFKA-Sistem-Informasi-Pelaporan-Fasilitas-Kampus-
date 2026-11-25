@extends('layouts.app')

@section('title', 'Kelola fasilitas FMIPA')
@section('breadcrumb', 'SIPAFKA / Admin / Kelola fasilitas FMIPA')
@section('judul', 'Kelola fasilitas FMIPA')
@section('subjudul', 'Data fasilitas dan lokasi yang tersedia untuk pelaporan.')

@push('styles')
    <style>
        .adm-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.55fr) minmax(290px, 1fr);
            gap: 18px;
            align-items: start;
        }

        .adm-panel-card {
            padding: 0;
            overflow: hidden;
        }

        .adm-search-box {
            padding: 18px 20px 16px;
            border-bottom: 1px solid var(--sip-border);
        }

        .adm-search-row {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .adm-section-head {
            padding: 16px 20px 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .adm-section-title {
            margin: 0;
            font-size: 15px;
            font-weight: 700;
            color: var(--sip-text);
        }

        .adm-card-title {
            margin: 0 0 4px;
            font-size: 16px;
            font-weight: 700;
            color: var(--sip-text);
        }

        .adm-card-sub {
            margin: 0 0 18px;
            font-size: 12px;
            color: var(--sip-text-subtle);
        }

        .adm-code-cell {
            font-weight: 600;
            color: var(--sip-text-secondary);
            white-space: nowrap;
        }

        .adm-name-cell {
            font-weight: 600;
            color: var(--sip-text);
        }

        .adm-sub-note {
            margin-top: 2px;
            font-size: 11.5px;
            color: var(--sip-text-subtle);
            font-weight: 400;
        }

        .adm-lokasi-cell {
            color: var(--sip-text-muted);
        }

        .adm-action-link {
            font-size: 12.5px;
            font-weight: 600;
            color: var(--sip-primary);
            white-space: nowrap;
        }

        .adm-action-link:hover {
            text-decoration: underline;
        }

        .adm-empty-cell {
            text-align: center;
            padding: 28px 18px !important;
            color: var(--sip-text-muted);
        }

        .adm-table-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 12px 20px;
            border-top: 1px solid var(--sip-border);
            background-color: var(--sip-surface);
            font-size: 11.5px;
            color: var(--sip-text-subtle);
        }

        .adm-pagination {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .adm-page-links {
            display: inline-flex;
            gap: 6px;
        }

        .adm-page-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 6px;
            border: 1px solid var(--sip-border);
            background-color: var(--sip-surface);
            color: var(--sip-text);
            font-weight: 600;
        }

        .adm-page-btn.is-disabled {
            color: var(--sip-text-subtle);
            background-color: var(--sip-surface-muted);
        }

        .adm-form-note {
            margin: 12px 0 0;
            font-size: 11.5px;
            color: var(--sip-text-subtle);
        }

        .adm-kode-preview {
            margin-top: 6px;
            font-size: 11.5px;
            color: var(--sip-text-muted);
        }

        @media (max-width: 960px) {
            .adm-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .adm-table-footer {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
@endpush

@section('content')
    <div class="adm-grid">
        <section class="card adm-panel-card">
            <div class="adm-search-box">
                <form action="{{ route('admin.fasilitas.index') }}" method="GET" id="formCariFasilitas">
                    <label for="inputCariFasilitas" class="form-label">Pencarian</label>
                    <div class="adm-search-row">
                        <input
                            type="text"
                            id="inputCariFasilitas"
                            name="cari"
                            value="{{ $cari }}"
                            placeholder="Cari fasilitas atau lokasi"
                            class="form-control"
                        >
                        @if ($cari !== '')
                            <a href="{{ route('admin.fasilitas.index') }}" class="btn btn-secondary">Reset</a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="adm-section-head">
                <h2 class="adm-section-title">Data fasilitas FMIPA</h2>
            </div>

            <div class="table-wrap">
                <table class="sip-table">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Fasilitas</th>
                            <th>Lokasi FMIPA</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($fasilitasList as $item)
                            <tr>
                                <td class="adm-code-cell">{{ $item->kode }}</td>
                                <td class="adm-name-cell">
                                    {{ $item->nama }}
                                    @if (filled($item->keterangan))
                                        <div class="adm-sub-note">{{ $item->keterangan }}</div>
                                    @endif
                                </td>
                                <td class="adm-lokasi-cell">
                                    {{ $item->ruang }}
                                    @if ($item->gedung && $item->gedung !== 'Gedung FMIPA')
                                        <div class="adm-sub-note">{{ $item->gedung }}</div>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.fasilitas.edit', $item) }}" class="adm-action-link">Ubah</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="adm-empty-cell">
                                    Belum ada data fasilitas yang sesuai.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="adm-table-footer">
                <span>
                    @if ($fasilitasList->total() > 0)
                        Menampilkan {{ $fasilitasList->firstItem() }}&ndash;{{ $fasilitasList->lastItem() }} dari {{ $fasilitasList->total() }} data
                    @else
                        Menampilkan 0 data
                    @endif
                </span>

                <div class="adm-pagination">
                    <span>Halaman {{ $fasilitasList->currentPage() }} dari {{ $fasilitasList->lastPage() }}</span>
                    @if ($fasilitasList->hasPages())
                        <div class="adm-page-links">
                            @if ($fasilitasList->onFirstPage())
                                <span class="adm-page-btn is-disabled">&lsaquo;</span>
                            @else
                                <a href="{{ $fasilitasList->previousPageUrl() }}" class="adm-page-btn" aria-label="Halaman sebelumnya">&lsaquo;</a>
                            @endif

                            @if ($fasilitasList->hasMorePages())
                                <a href="{{ $fasilitasList->nextPageUrl() }}" class="adm-page-btn" aria-label="Halaman berikutnya">&rsaquo;</a>
                            @else
                                <span class="adm-page-btn is-disabled">&rsaquo;</span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </section>

        <aside class="card">
            <h2 class="adm-card-title">Tambah fasilitas</h2>
            <p class="adm-card-sub">Lengkapi data bertanda *.</p>

            <form action="{{ route('admin.fasilitas.store') }}" method="POST" novalidate>
                @csrf

                <div class="form-group">
                    <label for="nama" class="form-label">Nama fasilitas *</label>
                    <input
                        type="text"
                        id="nama"
                        name="nama"
                        value="{{ old('nama') }}"
                        placeholder="Contoh: AC"
                        maxlength="120"
                        required
                        class="form-control @error('nama') is-invalid @enderror"
                    >
                    @error('nama')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="lokasi" class="form-label">Lokasi FMIPA *</label>
                    <input
                        type="text"
                        id="lokasi"
                        name="lokasi"
                        value="{{ old('lokasi') }}"
                        placeholder="Contoh: Ruang kuliah B.204"
                        maxlength="150"
                        required
                        class="form-control @error('lokasi') is-invalid @enderror"
                    >
                    @error('lokasi')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="keterangan" class="form-label">Keterangan</label>
                    <textarea
                        id="keterangan"
                        name="keterangan"
                        rows="3"
                        maxlength="500"
                        placeholder="Informasi tambahan fasilitas"
                        class="form-control @error('keterangan') is-invalid @enderror"
                    >{{ old('keterangan') }}</textarea>
                    @error('keterangan')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Simpan data</button>

                <p class="adm-form-note">Hanya daftarkan fasilitas di lingkungan FMIPA UHO.</p>
                <p class="adm-kode-preview">Kode otomatis berikutnya: <strong>{{ $kodeBerikutnya }}</strong></p>
            </form>
        </aside>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('formCariFasilitas');
            const input = document.getElementById('inputCariFasilitas');
            if (!form || !input) return;

            let timer = null;
            input.addEventListener('input', function () {
                clearTimeout(timer);
                timer = setTimeout(function () {
                    form.submit();
                }, 400);
            });
        });
    </script>
@endpush
