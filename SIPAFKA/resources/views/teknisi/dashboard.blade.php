@extends('layouts.app')

@section('title', 'Dashboard teknisi')
@section('breadcrumb', 'SIPAFKA / Teknisi / Ringkasan')
@section('judul', 'Dashboard teknisi')
@section('subjudul', 'Kelola tugas dan catat perkembangan perbaikan.')

@push('styles')
<style>
    .tk-dash-banner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 18px;
    }
    .tk-dash-banner-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--sip-text);
    }
    .tk-dash-banner-sub {
        margin-top: 2px;
        font-size: 12px;
        color: var(--sip-text-muted);
    }
    .tk-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 24px;
    }
    .tk-stat-card {
        padding: 16px 18px;
    }
    .tk-stat-label {
        font-size: 11.5px;
        font-weight: 500;
        color: var(--sip-text-muted);
    }
    .tk-stat-value {
        margin: 8px 0 4px;
        font-size: 28px;
        font-weight: 700;
        line-height: 1.1;
        color: var(--sip-text);
    }
    .tk-stat-hint {
        font-size: 11px;
        color: var(--sip-text-subtle);
    }
    .tk-section-head {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 12px;
    }
    .tk-section-title {
        margin: 0;
        font-size: 15px;
        font-weight: 700;
        color: var(--sip-text);
    }
    .tk-col-nomor {
        font-weight: 600;
        color: var(--sip-text-secondary);
        white-space: nowrap;
    }
    .tk-row-title {
        font-weight: 600;
        color: var(--sip-text);
    }
    .tk-row-sub {
        margin-top: 2px;
        font-size: 11.5px;
        color: var(--sip-text-subtle);
    }
    .tk-col-date {
        color: var(--sip-text-muted);
        white-space: nowrap;
    }
    .tk-action-link {
        font-size: 12.5px;
        font-weight: 600;
        color: var(--sip-primary);
        white-space: nowrap;
    }
    .tk-action-link:hover {
        text-decoration: underline;
    }
    .tk-empty-cell {
        text-align: center;
        padding: 28px 18px !important;
        color: var(--sip-text-muted);
    }
    .tk-table-footer {
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
    .tk-note-bar {
        margin-top: 16px;
        padding: 12px 16px;
        border-radius: var(--sip-radius-md);
        border: 1px solid var(--sip-border);
        background-color: var(--sip-surface-muted);
        font-size: 12px;
        color: var(--sip-text-muted);
    }
    @media (max-width: 960px) {
        .tk-stats-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
    @media (max-width: 640px) {
        .tk-dash-banner {
            flex-direction: column;
            align-items: flex-start;
        }
        .tk-stats-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
    <section class="info-banner tk-dash-banner">
        <div>
            <div class="tk-dash-banner-title">Selamat bekerja, {{ $namaDepan }}.</div>
            <div class="tk-dash-banner-sub">Ada {{ $ditugaskan }} tugas baru yang menunggu penanganan Anda.</div>
        </div>
        <a href="{{ route('teknisi.tugas.index') }}" class="btn btn-primary">Lihat daftar tugas</a>
    </section>

    <section class="tk-stats-grid" aria-label="Ringkasan tugas perbaikan">
        <article class="card tk-stat-card">
            <div class="tk-stat-label">Total tugas</div>
            <div class="tk-stat-value">{{ $totalTugas }}</div>
            <div class="tk-stat-hint">Penugasan kepada Anda</div>
        </article>

        <article class="card tk-stat-card">
            <div class="tk-stat-label">Ditugaskan</div>
            <div class="tk-stat-value">{{ $ditugaskan }}</div>
            <div class="tk-stat-hint">Siap untuk dikerjakan</div>
        </article>

        <article class="card tk-stat-card">
            <div class="tk-stat-label">Diproses</div>
            <div class="tk-stat-value">{{ $diproses }}</div>
            <div class="tk-stat-hint">Sedang dalam penanganan</div>
        </article>

        <article class="card tk-stat-card">
            <div class="tk-stat-label">Selesai</div>
            <div class="tk-stat-value">{{ $selesai }}</div>
            <div class="tk-stat-hint">Perbaikan telah dicatat</div>
        </article>
    </section>

    <div class="tk-section-head">
        <h2 class="tk-section-title">Tugas terbaru</h2>
        <a href="{{ route('teknisi.tugas.index') }}" class="tk-action-link">Lihat semua &rarr;</a>
    </div>

    <div class="card-table">
        <div class="table-wrap">
            <table class="sip-table">
                <thead>
                    <tr>
                        <th>Laporan</th>
                        <th>Kerusakan &amp; Lokasi</th>
                        <th>Ditugaskan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tugasTerbaru as $tugas)
                        <tr>
                            <td class="tk-col-nomor">{{ $tugas->nomor_laporan }}</td>
                            <td>
                                <div class="tk-row-title">{{ $tugas->judul }}</div>
                                <div class="tk-row-sub">
                                    {{ $tugas->fasilitas?->ruang ?? '-' }}
                                </div>
                            </td>
                            <td class="tk-col-date">
                                {{ $tugas->penugasan?->tanggal_tugas?->translatedFormat('d M Y') ?? $tugas->tanggal?->translatedFormat('d M Y') }}
                            </td>
                            <td>
                                <x-status-badge :status="$tugas->status" />
                            </td>
                            <td>
                                <a href="{{ route('teknisi.tugas.show', $tugas) }}" class="tk-action-link">
                                    Detail tugas &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="tk-empty-cell">
                                Belum ada tugas perbaikan yang ditugaskan kepada Anda.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="tk-table-footer">
            <span>{{ $tugasTerbaru->count() }} tugas terbaru</span>
            <a href="{{ route('teknisi.tugas.index') }}" class="tk-action-link">Buka daftar tugas &rarr;</a>
        </div>
    </div>

    <div class="tk-note-bar">
        Perbarui status dan catatan setelah setiap perkembangan agar pelapor dapat memantau perbaikan.
    </div>
@endsection
