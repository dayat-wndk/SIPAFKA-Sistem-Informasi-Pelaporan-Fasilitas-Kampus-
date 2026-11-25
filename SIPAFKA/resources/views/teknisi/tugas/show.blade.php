@extends('layouts.app')

@section('title', $laporan->nomor_laporan . ' — Detail tugas')
@section('breadcrumb', 'SIPAFKA / Teknisi / Detail tugas')
@section('judul', 'Detail tugas')
@section('subjudul', 'Tinjau kerusakan dan mulai penanganan fasilitas.')

@push('styles')
    <style>
        .tk-detail-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            margin-bottom: 18px;
            flex-wrap: wrap;
        }

        .tk-detail-title {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            color: var(--sip-text);
        }

        .tk-detail-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.5fr) minmax(270px, 1fr);
            gap: 18px;
            align-items: start;
        }

        .tk-card-title {
            margin: 0 0 4px;
            font-size: 16px;
            font-weight: 700;
            color: var(--sip-text);
        }

        .tk-field-block {
            margin-top: 16px;
        }

        .tk-field-label {
            font-size: 11px;
            color: var(--sip-text-subtle);
            margin-bottom: 4px;
        }

        .tk-field-value {
            font-size: 13.5px;
            font-weight: 600;
            color: var(--sip-text);
        }

        .tk-field-desc {
            font-size: 13px;
            line-height: 1.6;
            color: var(--sip-text-secondary);
        }

        .tk-photo-card {
            margin-top: 16px;
            border: 1px solid var(--sip-border);
            border-radius: 10px;
            padding: 12px;
            background-color: var(--sip-surface-muted);
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .tk-photo-link {
            flex-shrink: 0;
        }

        .tk-photo-thumb {
            width: 72px;
            height: 54px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid var(--sip-border);
            display: block;
        }

        .tk-photo-meta {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }

        .tk-file-ext {
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

        .tk-photo-name {
            font-size: 12.5px;
            font-weight: 600;
            color: var(--sip-text);
            word-break: break-word;
        }

        .tk-photo-sub {
            font-size: 11.5px;
            color: var(--sip-text-subtle);
        }

        .tk-note-banner {
            margin-top: 16px;
        }

        .tk-note-title {
            font-size: 12px;
            font-weight: 700;
            color: var(--sip-primary);
            margin-bottom: 4px;
        }

        .tk-note-text {
            margin: 0;
            font-size: 12.5px;
            color: var(--sip-text-secondary);
        }

        .tk-assign-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
            margin-top: 14px;
        }

        .tk-detail-actions {
            margin-top: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .tk-inline-form {
            margin: 0;
        }

        @media (max-width: 960px) {
            .tk-detail-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')
    <div class="tk-detail-head">
        <h2 class="tk-detail-title">{{ $laporan->nomor_laporan }} &bull; {{ $laporan->judul }}</h2>
        <x-status-badge :status="$laporan->status" />
    </div>

    <div class="tk-detail-grid">
        <section class="card">
            <h3 class="tk-card-title">Detail kerusakan</h3>

            <div class="tk-field-block">
                <div class="tk-field-label">Fasilitas &amp; lokasi</div>
                <div class="tk-field-value">
                    {{ $laporan->fasilitas?->nama ?? '-' }}
                    &bull;
                    {{ $laporan->fasilitas?->ruang ?? '-' }},
                    {{ $laporan->fasilitas?->gedung ?? '-' }}
                </div>
            </div>

            <div class="tk-field-block">
                <div class="tk-field-label">Deskripsi pelapor</div>
                <div class="tk-field-desc">{{ $laporan->deskripsi }}</div>
            </div>

            @if ($laporan->foto)
                <div class="tk-photo-card">
                    @if ($laporan->foto_url)
                        <a href="{{ $laporan->foto_url }}" target="_blank" rel="noopener noreferrer" class="tk-photo-link">
                            <img
                                src="{{ $laporan->foto_url }}"
                                alt="Foto kerusakan {{ $laporan->nomor_laporan }}"
                                class="tk-photo-thumb"
                            >
                        </a>
                    @endif
                    <div class="tk-photo-meta">
                        <span class="tk-file-ext">{{ strtoupper(pathinfo($laporan->foto, PATHINFO_EXTENSION) ?: 'IMG') }}</span>
                        <div>
                            <div class="tk-photo-name">{{ basename($laporan->foto) }}</div>
                            <div class="tk-photo-sub">
                                @if (!empty($ukuranFoto))
                                    Foto kerusakan &bull; {{ $ukuranFoto }}
                                @else
                                    Foto kerusakan yang dilampirkan pelapor
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="info-banner tk-note-banner">
                <div class="tk-note-title">Catatan penugasan</div>
                <p class="tk-note-text">
                    {{ $laporan->penugasan?->catatan ?: 'Tidak ada catatan penugasan khusus dari admin.' }}
                </p>
            </div>
        </section>

        <aside class="card">
            <h3 class="tk-card-title">Penugasan</h3>

            <div class="tk-assign-list">
                <div>
                    <div class="tk-field-label">Teknisi</div>
                    <div class="tk-field-value">{{ $laporan->penugasan?->teknisi?->name ?? '-' }}</div>
                </div>

                <div>
                    <div class="tk-field-label">Ditugaskan oleh</div>
                    <div class="tk-field-value">{{ $laporan->penugasan?->admin?->name ?? 'Admin FMIPA' }}</div>
                </div>

                <div>
                    <div class="tk-field-label">Tanggal penugasan</div>
                    <div class="tk-field-value">
                        {{ $laporan->penugasan?->tanggal_tugas?->translatedFormat('d F Y, H.i') ?? '-' }}
                    </div>
                </div>

                <div>
                    <div class="tk-field-label">Pelapor</div>
                    <div class="tk-field-value">{{ $laporan->pelapor?->name ?? '-' }}</div>
                </div>

                @if ($laporan->penugasan?->catatan)
                    <div>
                        <div class="tk-field-label">Catatan penugasan</div>
                        <div class="tk-field-desc">{{ $laporan->penugasan->catatan }}</div>
                    </div>
                @endif
            </div>
        </aside>
    </div>

    <div class="tk-detail-actions">
        <a href="{{ route('teknisi.tugas.index') }}" class="btn btn-secondary">
            &larr; Daftar tugas
        </a>

        @if ($laporan->status === \App\Enums\StatusLaporan::Ditugaskan)
            <form action="{{ route('teknisi.tugas.start', $laporan) }}" method="POST" class="tk-inline-form">
                @csrf
                <button type="submit" class="btn btn-primary">Mulai perbaikan</button>
            </form>
        @elseif ($laporan->status === \App\Enums\StatusLaporan::Diproses)
            <a href="{{ route('teknisi.tugas.edit', $laporan) }}" class="btn btn-primary">
                Perbarui status
            </a>
        @elseif ($laporan->status === \App\Enums\StatusLaporan::Selesai)
            <a href="{{ route('teknisi.tugas.completed', $laporan) }}" class="btn btn-primary">
                Lihat ringkasan selesai
            </a>
        @endif
    </div>
@endsection
