@extends('layouts.app')

@section('title', 'Dashboard admin')
@section('breadcrumb', 'SIPAFKA / Admin / Ringkasan')
@section('judul', 'Dashboard admin')
@section('subjudul', 'Ringkasan penanganan fasilitas di lingkungan FMIPA UHO.')

@push('styles')
<style>
    .adm-dash-banner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 18px;
    }
    .adm-dash-banner-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--sip-text);
    }
    .adm-dash-banner-sub {
        margin-top: 2px;
        font-size: 12px;
        color: var(--sip-text-muted);
    }
    .adm-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 18px;
    }
    .adm-stat-card {
        padding: 16px 18px;
    }
    .adm-stat-label {
        font-size: 11.5px;
        font-weight: 500;
        color: var(--sip-text-muted);
    }
    .adm-stat-value {
        margin: 8px 0 4px;
        font-size: 28px;
        font-weight: 700;
        line-height: 1.1;
        color: var(--sip-text);
    }
    .adm-stat-hint {
        font-size: 11px;
        color: var(--sip-text-subtle);
    }
    .adm-dist-card {
        margin-bottom: 22px;
        padding: 18px 20px;
    }
    .adm-dist-title {
        margin: 0 0 14px;
        font-size: 14px;
        font-weight: 700;
        color: var(--sip-text);
    }
    .adm-dist-grid {
        display: grid;
        grid-template-columns: repeat(6, minmax(0, 1fr));
        gap: 14px;
    }
    .adm-dist-item {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .adm-dist-count {
        font-size: 13px;
        font-weight: 700;
        color: var(--sip-text);
    }
    .adm-dist-track {
        height: 4px;
        width: 100%;
        background-color: #E2E8F0;
        border-radius: 999px;
        overflow: hidden;
    }
    .adm-dist-fill {
        height: 100%;
        border-radius: 999px;
    }
    .adm-section-head {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 12px;
    }
    .adm-section-title {
        margin: 0;
        font-size: 15px;
        font-weight: 700;
        color: var(--sip-text);
    }
    .adm-col-nomor {
        font-weight: 600;
        color: var(--sip-text-secondary);
        white-space: nowrap;
    }
    .adm-row-title {
        font-weight: 600;
        color: var(--sip-text);
    }
    .adm-row-sub {
        margin-top: 2px;
        font-size: 11.5px;
        color: var(--sip-text-subtle);
    }
    .adm-col-date {
        color: var(--sip-text-muted);
        white-space: nowrap;
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
        padding: 12px 18px;
        border-top: 1px solid var(--sip-border);
        background-color: var(--sip-surface);
        font-size: 11.5px;
        color: var(--sip-text-subtle);
    }
    @media (max-width: 960px) {
        .adm-stats-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .adm-dist-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }
    @media (max-width: 640px) {
        .adm-dash-banner {
            flex-direction: column;
            align-items: flex-start;
        }
        .adm-stats-grid,
        .adm-dist-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
    <section class="info-banner adm-dash-banner">
        <div>
            <div class="adm-dash-banner-title">{{ $menungguVerifikasi }} laporan menunggu verifikasi</div>
            <div class="adm-dash-banner-sub">Periksa kelengkapan laporan sebelum menugaskan teknisi.</div>
        </div>
        <a href="{{ route('admin.laporan.index') }}" class="btn btn-primary">Tinjau laporan masuk</a>
    </section>

    <section class="adm-stats-grid" aria-label="Ringkasan laporan FMIPA">
        <article class="card adm-stat-card">
            <div class="adm-stat-label">Total laporan</div>
            <div class="adm-stat-value">{{ $totalLaporan }}</div>
            <div class="adm-stat-hint">Seluruh laporan FMIPA</div>
        </article>

        <article class="card adm-stat-card">
            <div class="adm-stat-label">Ditugaskan</div>
            <div class="adm-stat-value">{{ $ditugaskan }}</div>
            <div class="adm-stat-hint">Menunggu penanganan teknisi</div>
        </article>

        <article class="card adm-stat-card">
            <div class="adm-stat-label">Diproses</div>
            <div class="adm-stat-value">{{ $diproses }}</div>
            <div class="adm-stat-hint">Perbaikan sedang berlangsung</div>
        </article>

        <article class="card adm-stat-card">
            <div class="adm-stat-label">Selesai</div>
            <div class="adm-stat-value">{{ $selesai }}</div>
            <div class="adm-stat-hint">Perbaikan telah dituntaskan</div>
        </article>
    </section>

    <section class="card adm-dist-card" aria-label="Distribusi status laporan">
        <h2 class="adm-dist-title">Distribusi status laporan</h2>

        <div class="adm-dist-grid">
            @foreach ($distribusi as $item)
                <div class="adm-dist-item">
                    <div>
                        <x-status-badge :status="$item['status']" />
                    </div>
                    <div class="adm-dist-count">{{ $item['jumlah'] }} laporan</div>
                    <div class="adm-dist-track">
                        <div
                            class="adm-dist-fill"
                            style="width: {{ $item['persen'] }}%; background-color: {{ $item['warna'] }};"
                        ></div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <div class="adm-section-head">
        <h2 class="adm-section-title">Laporan terbaru</h2>
        <a href="{{ route('admin.laporan.index') }}" class="adm-action-link">Lihat semua &rarr;</a>
    </div>

    <div class="card-table">
        <div class="table-wrap">
            <table class="sip-table">
                <thead>
                    <tr>
                        <th>Nomor</th>
                        <th>Laporan / Lokasi / Pelapor</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($laporanTerbaru as $laporan)
                        <tr>
                            <td class="adm-col-nomor">{{ $laporan->nomor_laporan }}</td>
                            <td>
                                <div class="adm-row-title">{{ $laporan->judul }}</div>
                                <div class="adm-row-sub">
                                    {{ $laporan->fasilitas?->ruang ?? '-' }}
                                    &bull;
                                    {{ $laporan->pelapor?->name ?? '-' }}
                                </div>
                            </td>
                            <td class="adm-col-date">{{ $laporan->tanggal?->translatedFormat('d M Y') }}</td>
                            <td>
                                <x-status-badge :status="$laporan->status" />
                            </td>
                            <td>
                                @if ($laporan->status === \App\Enums\StatusLaporan::Diverifikasi)
                                    <a href="{{ route('admin.laporan.assign', $laporan) }}" class="adm-action-link">
                                        Tugaskan &rarr;
                                    </a>
                                @else
                                    <a href="{{ route('admin.laporan.show', $laporan) }}" class="adm-action-link">
                                        Tinjau &rarr;
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="adm-empty-cell">
                                Belum ada laporan masuk yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="adm-table-footer">
            <span>{{ $laporanTerbaru->count() }} laporan terbaru</span>
            <a href="{{ route('admin.laporan.index') }}" class="adm-action-link">Buka laporan masuk &rarr;</a>
        </div>
    </div>
@endsection
