@extends('layouts.app')

@section('title', 'Ubah fasilitas FMIPA')
@section('breadcrumb', 'SIPAFKA / Admin / Kelola fasilitas FMIPA / Ubah')
@section('judul', 'Ubah data fasilitas FMIPA')
@section('subjudul', 'Perbarui nama fasilitas, lokasi FMIPA, dan keterangan.')

@push('styles')
    <style>
        .adm-edit-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.6fr) minmax(260px, 1fr);
            gap: 18px;
            align-items: start;
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

        .adm-form-actions {
            margin-top: 20px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
        }

        .adm-meta-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
            margin-top: 12px;
        }

        .adm-meta-label {
            font-size: 11px;
            color: var(--sip-text-subtle);
            margin-bottom: 3px;
        }

        .adm-meta-value {
            font-size: 13px;
            font-weight: 600;
            color: var(--sip-text);
            word-break: break-word;
        }

        .adm-field-hint {
            margin-top: 5px;
            font-size: 11.5px;
            color: var(--sip-text-subtle);
        }

        @media (max-width: 960px) {
            .adm-edit-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')
    <div class="adm-edit-grid">
        <section class="card">
            <h2 class="adm-card-title">Detail fasilitas</h2>
            <p class="adm-card-sub">Kode fasilitas {{ $fasilitas->kode }} tetap terjaga dan tidak berubah.</p>

            <form action="{{ route('admin.fasilitas.update', $fasilitas) }}" method="POST" novalidate>
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="kode_fasilitas" class="form-label">Kode fasilitas</label>
                    <input
                        type="text"
                        id="kode_fasilitas"
                        value="{{ $fasilitas->kode }}"
                        disabled
                        class="form-control"
                        style="background-color: var(--sip-surface-muted); color: var(--sip-text-muted);"
                    >
                </div>

                <div class="form-group">
                    <label for="nama" class="form-label">Nama fasilitas *</label>
                    <input
                        type="text"
                        id="nama"
                        name="nama"
                        value="{{ old('nama', $fasilitas->nama) }}"
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
                        value="{{ old('lokasi', $fasilitas->ruang) }}"
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
                    <label for="gedung" class="form-label">Gedung FMIPA</label>
                    <input
                        type="text"
                        id="gedung"
                        name="gedung"
                        value="{{ old('gedung', $fasilitas->gedung) }}"
                        placeholder="Gedung FMIPA"
                        maxlength="120"
                        class="form-control @error('gedung') is-invalid @enderror"
                    >
                    <div class="adm-field-hint">Lingkungan FMIPA Universitas Halu Oleo (default: Gedung FMIPA).</div>
                    @error('gedung')
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
                    >{{ old('keterangan', $fasilitas->keterangan) }}</textarea>
                    @error('keterangan')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="adm-form-actions">
                    <a href="{{ route('admin.fasilitas.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan perubahan</button>
                </div>
            </form>
        </section>

        <aside class="card">
            <h2 class="adm-card-title">Ringkasan fasilitas</h2>
            <div class="adm-meta-list">
                <div>
                    <div class="adm-meta-label">Kode fasilitas</div>
                    <div class="adm-meta-value">{{ $fasilitas->kode }}</div>
                </div>
                <div>
                    <div class="adm-meta-label">Lokasi tersimpan</div>
                    <div class="adm-meta-value">{{ $fasilitas->ruang }}, {{ $fasilitas->gedung }}</div>
                </div>
                <div>
                    <div class="adm-meta-label">Laporan terkait</div>
                    <div class="adm-meta-value">{{ $fasilitas->laporan()->count() }} laporan</div>
                </div>
            </div>

            <div class="info-banner" style="margin-top: 18px;">
                <div style="font-size: 12px; font-weight: 700; color: var(--sip-primary); margin-bottom: 4px;">
                    Terhubung ke formulir pelapor
                </div>
                <p style="margin: 0; font-size: 12px; color: var(--sip-text-secondary);">
                    Data fasilitas ini langsung tampil pada pilihan fasilitas dan lokasi saat pelapor membuat laporan kerusakan (Modul 2).
                </p>
            </div>
        </aside>
    </div>
@endsection
