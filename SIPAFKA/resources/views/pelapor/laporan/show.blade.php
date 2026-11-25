@extends('layouts.app')

@section('title', $laporan->nomor_laporan . ' — Detail laporan')
@section('breadcrumb', 'SIPAFKA / Pelapor / Detail laporan')
@section('judul', 'Detail laporan')
@section('subjudul', 'Lacak progres penanganan secara transparan.')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pelapor.css') }}">
@endpush

@section('content')
    <div class="pl-detail-head">
        <div>
            <h2 class="pl-detail-title">{{ $laporan->nomor_laporan }} &bull; {{ $laporan->judul }}</h2>
            <p class="pl-detail-meta">
                Dilaporkan oleh {{ $laporan->pelapor?->name ?? 'Pelapor' }}
                &bull;
                {{ $laporan->tanggal?->translatedFormat('d M Y') }}
            </p>
        </div>

        <x-status-badge :status="$laporan->status" />
    </div>

    <div class="pl-detail-grid">
        <section class="card">
            <h3 class="pl-card-title">Informasi laporan</h3>

            <div class="pl-field-block">
                <div class="pl-field-label">Fasilitas</div>
                <div class="pl-field-value">
                    {{ $laporan->fasilitas?->nama ?? '-' }}
                    &bull;
                    {{ $laporan->fasilitas?->ruang ?? '-' }},
                    {{ $laporan->fasilitas?->gedung ?? '-' }}
                </div>
            </div>

            <div class="pl-field-block">
                <div class="pl-field-label">Deskripsi kerusakan</div>
                <div class="pl-field-desc">{{ $laporan->deskripsi }}</div>
            </div>

            @if ($laporan->foto)
                <div class="pl-photo-card">
                    @if ($laporan->foto_url)
                        <a href="{{ $laporan->foto_url }}" target="_blank" rel="noopener noreferrer" class="pl-photo-link">
                            <img
                                src="{{ $laporan->foto_url }}"
                                alt="Foto kerusakan {{ $laporan->nomor_laporan }}"
                                class="pl-photo-thumb"
                            >
                        </a>
                    @endif
                    <div class="pl-photo-meta">
                        <span class="pl-file-ext">{{ strtoupper(pathinfo($laporan->foto, PATHINFO_EXTENSION) ?: 'IMG') }}</span>
                        <div>
                            <div class="pl-photo-name">{{ basename($laporan->foto) }}</div>
                            <div class="pl-photo-sub">
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

            @if ($laporan->status === \App\Enums\StatusLaporan::Ditolak)
                <div class="pl-reject-box" role="alert">
                    <div class="pl-reject-title">Alasan penolakan</div>
                    <p class="pl-reject-text">
                        {{ $laporan->alasan_penolakan ?: 'Laporan tidak dapat diproses lebih lanjut oleh admin.' }}
                    </p>
                </div>
            @elseif ($perbaikanTerakhir)
                <div class="info-banner pl-latest-note">
                    <div class="pl-latest-note-title">
                        Catatan terbaru &bull; {{ $perbaikanTerakhir->tanggal?->translatedFormat('d M, H.i') }}
                    </div>
                    <p class="pl-latest-note-text">
                        {{ $perbaikanTerakhir->hasil ?: $perbaikanTerakhir->catatan_progres }}
                    </p>
                </div>
            @endif

            <div class="pl-teknisi-row">
                Teknisi: <strong>{{ $laporan->penugasan?->teknisi?->name ?? 'Belum ditugaskan' }}</strong>
            </div>
        </section>

        <aside class="card">
            <h3 class="pl-card-title">Perkembangan laporan</h3>
            @include('pelapor.laporan._timeline')
        </aside>
    </div>

    <div class="pl-detail-actions">
        <a href="{{ route('pelapor.laporan.index') }}" class="btn btn-secondary">&larr; Kembali ke riwayat</a>
    </div>
@endsection
