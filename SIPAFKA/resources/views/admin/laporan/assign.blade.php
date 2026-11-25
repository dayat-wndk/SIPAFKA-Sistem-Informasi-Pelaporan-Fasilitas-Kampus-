@extends('layouts.app')

@section('title', $laporan->nomor_laporan . ' — Penugasan teknisi')
@section('breadcrumb', 'SIPAFKA / Admin / Penugasan teknisi')
@section('judul', 'Penugasan teknisi')
@section('subjudul', 'Pilih teknisi untuk menangani laporan yang diverifikasi.')

@push('styles')
<style>
    .adm-assign-head {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 18px;
    }
    .adm-assign-title {
        margin: 0;
        font-size: 18px;
        font-weight: 700;
        color: var(--sip-text);
    }
    .adm-assign-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.55fr) minmax(260px, 1fr);
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
    .adm-info-note {
        margin: 4px 0 18px;
        font-size: 12.5px;
        color: var(--sip-primary);
        line-height: 1.5;
    }
    .adm-form-actions {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .adm-summary-list {
        margin-top: 14px;
        display: flex;
        flex-direction: column;
        gap: 14px;
    }
    .adm-summary-label {
        font-size: 11px;
        color: var(--sip-text-subtle);
        margin-bottom: 2px;
    }
    .adm-summary-value {
        font-size: 13px;
        font-weight: 600;
        color: var(--sip-text);
    }
    @media (max-width: 900px) {
        .adm-assign-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
    <div class="adm-assign-head">
        <h2 class="adm-assign-title">{{ $laporan->nomor_laporan }} &bull; {{ $laporan->judul }}</h2>
        <x-status-badge :status="$laporan->status" />
    </div>

    <div class="adm-assign-grid">
        <section class="card">
            <h3 class="adm-card-title">Tentukan penanggung jawab</h3>
            <p class="adm-card-sub">
                {{ $laporan->fasilitas?->ruang ?? '-' }} &bull; {{ $laporan->fasilitas?->gedung ?? '-' }}
            </p>

            <form action="{{ route('admin.laporan.assign.store', $laporan) }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="teknisi_id" class="form-label">Teknisi *</label>
                    <select
                        id="teknisi_id"
                        name="teknisi_id"
                        class="form-control @error('teknisi_id') is-invalid @enderror"
                        required
                    >
                        <option value="">Pilih teknisi penanggung jawab</option>
                        @foreach ($teknisiList as $teknisi)
                            <option value="{{ $teknisi->id }}" @selected((string) old('teknisi_id') === (string) $teknisi->id)>
                                {{ $teknisi->name }} &mdash; Teknisi fasilitas
                            </option>
                        @endforeach
                    </select>
                    @error('teknisi_id')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="catatan" class="form-label">Catatan penugasan</label>
                    <textarea
                        id="catatan"
                        name="catatan"
                        rows="4"
                        class="form-control @error('catatan') is-invalid @enderror"
                        placeholder="Periksa kondisi kerusakan dan lakukan perbaikan sesuai standar unit..."
                    >{{ old('catatan') }}</textarea>
                    @error('catatan')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="info-banner adm-info-note">
                    Setelah disimpan, laporan berubah dari <strong>Diverifikasi</strong> menjadi
                    <strong>Ditugaskan</strong> dan muncul pada daftar tugas teknisi.
                </div>

                <div class="adm-form-actions">
                    <a href="{{ route('admin.laporan.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan penugasan</button>
                </div>
            </form>
        </section>

        <aside class="card">
            <h3 class="adm-card-title">Ringkasan laporan</h3>

            <div class="adm-summary-list">
                <div>
                    <div class="adm-summary-label">Pelapor</div>
                    <div class="adm-summary-value">{{ $laporan->pelapor?->name ?? '-' }}</div>
                </div>

                <div>
                    <div class="adm-summary-label">Fasilitas</div>
                    <div class="adm-summary-value">{{ $laporan->fasilitas?->nama ?? '-' }}</div>
                </div>

                <div>
                    <div class="adm-summary-label">Lokasi</div>
                    <div class="adm-summary-value">
                        {{ $laporan->fasilitas?->ruang ?? '-' }}@if ($laporan->fasilitas?->gedung), {{ $laporan->fasilitas->gedung }}@endif
                    </div>
                </div>

                <div>
                    <div class="adm-summary-label">Verifikasi</div>
                    <div class="adm-summary-value">
                        {{ $laporan->diverifikasi_at?->translatedFormat('d M Y') ?? '-' }}
                        &bull;
                        {{ $laporan->admin?->name ?? 'Admin FMIPA' }}
                    </div>
                </div>
            </div>
        </aside>
    </div>
@endsection
