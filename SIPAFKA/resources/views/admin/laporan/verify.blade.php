@extends('layouts.app')

@section('title', $laporan->nomor_laporan . ' — Verifikasi laporan')
@section('breadcrumb', 'SIPAFKA / Admin / Verifikasi laporan')
@section('judul', 'Verifikasi laporan')
@section('subjudul', 'Periksa kelengkapan dan kesesuaian laporan.')

@push('styles')
<style>
    .adm-hidden { display: none; }
    .adm-detail-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
        margin-bottom: 18px;
    }
    .adm-detail-title {
        margin: 0;
        font-size: 18px;
        font-weight: 700;
        color: var(--sip-text);
    }
    .adm-detail-meta {
        margin: 4px 0 0;
        font-size: 12px;
        color: var(--sip-text-subtle);
    }
    .adm-verify-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.45fr) minmax(280px, 1fr);
        gap: 18px;
        align-items: start;
    }
    .adm-card-title {
        margin: 0 0 4px;
        font-size: 16px;
        font-weight: 700;
        color: var(--sip-text);
    }
    .adm-field-block {
        margin-top: 16px;
    }
    .adm-field-label {
        font-size: 11px;
        color: var(--sip-text-subtle);
        margin-bottom: 4px;
    }
    .adm-field-value {
        font-size: 13.5px;
        font-weight: 600;
        color: var(--sip-text);
    }
    .adm-field-desc {
        font-size: 13px;
        line-height: 1.6;
        color: var(--sip-text-secondary);
    }
    .adm-photo-card {
        margin-top: 16px;
        border: 1px solid var(--sip-border);
        border-radius: 10px;
        padding: 12px;
        background-color: var(--sip-surface-muted);
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .adm-photo-thumb {
        width: 72px;
        height: 54px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid var(--sip-border);
        display: block;
    }
    .adm-photo-meta {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
    }
    .adm-file-ext {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 4px 7px;
        border-radius: 6px;
        background-color: #E2E8F0;
        color: var(--sip-text-secondary);
        font-size: 10px;
        font-weight: 700;
        flex-shrink: 0;
    }
    .adm-photo-name {
        font-size: 12.5px;
        font-weight: 600;
        color: var(--sip-text);
        word-break: break-word;
    }
    .adm-photo-sub {
        font-size: 11.5px;
        color: var(--sip-text-subtle);
    }
    .adm-guide-box {
        margin-top: 16px;
    }
    .adm-guide-title {
        font-size: 12px;
        font-weight: 700;
        color: var(--sip-primary);
        margin-bottom: 4px;
    }
    .adm-guide-text {
        margin: 0;
        font-size: 12px;
        color: var(--sip-text-secondary);
    }
    .adm-helper-note {
        margin: 0 0 14px;
        font-size: 12px;
        color: var(--sip-text-muted);
        line-height: 1.5;
    }
    .adm-btn-stack {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .adm-summary-list {
        margin-top: 14px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .adm-summary-item-label {
        font-size: 11px;
        color: var(--sip-text-subtle);
    }
    .adm-summary-item-val {
        font-size: 13px;
        font-weight: 600;
        color: var(--sip-text);
        margin-top: 2px;
    }
    .adm-back-row {
        margin-top: 18px;
    }
    @media (max-width: 900px) {
        .adm-verify-grid {
            grid-template-columns: 1fr;
        }
        .adm-detail-head {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>
@endpush

@section('content')
    <div class="adm-detail-head">
        <div>
            <h2 class="adm-detail-title">{{ $laporan->nomor_laporan }} &bull; {{ $laporan->judul }}</h2>
            <p class="adm-detail-meta">
                {{ ($laporan->created_at ?? $laporan->tanggal)?->translatedFormat('d F Y, H.i') }}
                &bull;
                {{ $laporan->pelapor?->name ?? 'Pelapor' }}
                &bull;
                {{ ucfirst($laporan->pelapor?->role ?? 'Pelapor') }}
            </p>
        </div>

        <x-status-badge :status="$laporan->status" />
    </div>

    <div class="adm-verify-grid">
        <section class="card">
            <h3 class="adm-card-title">Informasi kerusakan</h3>

            <div class="adm-field-block">
                <div class="adm-field-label">Fasilitas &amp; lokasi</div>
                <div class="adm-field-value">
                    {{ $laporan->fasilitas?->nama ?? '-' }}
                    &bull;
                    {{ $laporan->fasilitas?->ruang ?? '-' }},
                    {{ $laporan->fasilitas?->gedung ?? '-' }}
                </div>
            </div>

            <div class="adm-field-block">
                <div class="adm-field-label">Deskripsi pelapor</div>
                <div class="adm-field-desc">{{ $laporan->deskripsi }}</div>
            </div>

            @if ($laporan->foto)
                <div class="adm-photo-card">
                    @if ($laporan->foto_url)
                        <a href="{{ $laporan->foto_url }}" target="_blank" rel="noopener noreferrer">
                            <img
                                src="{{ $laporan->foto_url }}"
                                alt="Foto kerusakan {{ $laporan->nomor_laporan }}"
                                class="adm-photo-thumb"
                            >
                        </a>
                    @endif
                    <div class="adm-photo-meta">
                        <span class="adm-file-ext">{{ strtoupper(pathinfo($laporan->foto, PATHINFO_EXTENSION) ?: 'IMG') }}</span>
                        <div>
                            <div class="adm-photo-name">{{ basename($laporan->foto) }}</div>
                            <div class="adm-photo-sub">Klik gambar untuk melihat ukuran penuh</div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="info-banner adm-guide-box">
                <div class="adm-guide-title">Kesesuaian laporan</div>
                <p class="adm-guide-text">
                    Lokasi berada di FMIPA UHO. Pastikan foto dan deskripsi merujuk pada fasilitas yang dilaporkan.
                </p>
            </div>
        </section>

        <aside class="card">
            <h3 class="adm-card-title">Keputusan verifikasi</h3>

            @if ($laporan->status === \App\Enums\StatusLaporan::MenungguVerifikasi)
                <form method="POST" id="formVerifikasi" class="adm-field-block">
                    @csrf

                    <div class="form-group">
                        <label for="alasanPenolakanInput" class="form-label">
                            Catatan verifikasi / Alasan penolakan
                        </label>
                        <textarea
                            id="alasanPenolakanInput"
                            name="alasan_penolakan"
                            rows="5"
                            class="form-control @error('alasan_penolakan') is-invalid @enderror"
                            placeholder="Tuliskan catatan verifikasi atau alasan penolakan jika laporan tidak sesuai..."
                        >{{ old('alasan_penolakan') }}</textarea>
                        @error('alasan_penolakan')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                        <p class="form-error adm-hidden" id="clientRejectError"></p>
                    </div>

                    <p class="adm-helper-note">
                        Laporan yang disetujui akan berstatus <strong>Diverifikasi</strong> dan siap ditugaskan.
                        Penolakan wajib disertai alasan pada kolom di atas.
                    </p>

                    <div class="adm-btn-stack">
                        <button
                            type="submit"
                            formaction="{{ route('admin.laporan.approve', $laporan) }}"
                            id="btnVerifikasiLaporan"
                            class="btn btn-primary btn-block"
                        >
                            Verifikasi laporan
                        </button>
                        <button
                            type="submit"
                            formaction="{{ route('admin.laporan.reject', $laporan) }}"
                            id="btnTolakLaporan"
                            class="btn btn-secondary btn-block"
                        >
                            Tolak laporan
                        </button>
                    </div>
                </form>
            @else
                <div class="adm-summary-list">
                    <div>
                        <div class="adm-summary-item-label">Status saat ini</div>
                        <div class="adm-summary-item-val">
                            <x-status-badge :status="$laporan->status" />
                        </div>
                    </div>

                    <div>
                        <div class="adm-summary-item-label">Diverifikasi oleh</div>
                        <div class="adm-summary-item-val">
                            {{ $laporan->admin?->name ?? 'Admin FMIPA' }}
                            @if ($laporan->diverifikasi_at)
                                &bull; {{ $laporan->diverifikasi_at->translatedFormat('d M Y, H.i') }}
                            @endif
                        </div>
                    </div>

                    @if ($laporan->penugasan)
                        <div>
                            <div class="adm-summary-item-label">Teknisi ditugaskan</div>
                            <div class="adm-summary-item-val">
                                {{ $laporan->penugasan->teknisi?->name ?? '-' }}
                            </div>
                        </div>
                        @if ($laporan->penugasan->catatan)
                            <div>
                                <div class="adm-summary-item-label">Catatan penugasan</div>
                                <div class="adm-field-desc">{{ $laporan->penugasan->catatan }}</div>
                            </div>
                        @endif
                    @endif
                </div>

                @if ($laporan->status === \App\Enums\StatusLaporan::Diverifikasi)
                    <div class="adm-field-block">
                        <a href="{{ route('admin.laporan.assign', $laporan) }}" class="btn btn-primary btn-block">
                            Tugaskan teknisi &rarr;
                        </a>
                    </div>
                @endif
            @endif
        </aside>
    </div>

    <div class="adm-back-row">
        <a href="{{ route('admin.laporan.index') }}" class="btn btn-secondary">
            &larr; Kembali ke laporan masuk
        </a>
    </div>
@endsection

@push('scripts')
    @if (file_exists(resource_path('js/admin-verifikasi.js')))
        <script>{!! file_get_contents(resource_path('js/admin-verifikasi.js')) !!}</script>
    @endif
@endpush
