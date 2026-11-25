@extends('layouts.app')

@section('title', 'Dashboard pelapor')
@section('breadcrumb', 'SIPAFKA / Pelapor / Ringkasan')
@section('judul', 'Dashboard pelapor')
@section('subjudul', 'Pantau laporan Anda dan bantu jaga fasilitas kampus.')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pelapor.css') }}">
@endpush

@section('content')
    <section class="info-banner pl-hero-banner">
        <div>
            <div class="pl-hero-title">Kampus nyaman dimulai dari kita.</div>
            <div class="pl-hero-sub">Temukan fasilitas rusak? Laporkan agar segera ditangani.</div>
        </div>
        <a href="{{ route('pelapor.laporan.create') }}" class="btn btn-primary">+ Buat laporan</a>
    </section>

    <section class="pl-stats-grid" aria-label="Ringkasan status laporan">
        <article class="card pl-stat-card">
            <div class="pl-stat-label">Total laporan</div>
            <div class="pl-stat-value">{{ $totalLaporan }}</div>
            <div class="pl-stat-hint">Seluruh laporan Anda</div>
        </article>

        <article class="card pl-stat-card">
            <div class="pl-stat-label">Menunggu verifikasi</div>
            <div class="pl-stat-value">{{ $menungguVerifikasi }}</div>
            <div class="pl-stat-hint">Menunggu tinjauan admin</div>
        </article>

        <article class="card pl-stat-card">
            <div class="pl-stat-label">Dalam penanganan</div>
            <div class="pl-stat-value">{{ $dalamPenanganan }}</div>
            <div class="pl-stat-hint">Diverifikasi hingga diproses</div>
        </article>

        <article class="card pl-stat-card">
            <div class="pl-stat-label">Selesai</div>
            <div class="pl-stat-value">{{ $selesai }}</div>
            <div class="pl-stat-hint">Perbaikan telah selesai</div>
        </article>
    </section>

    <div class="pl-section-head">
        <h2 class="pl-section-title">Laporan terbaru</h2>
        <span class="pl-section-meta">Diperbarui {{ now()->translatedFormat('d F Y') }}</span>
    </div>

    <div class="card-table">
        @include('pelapor.laporan._tabel', ['laporanList' => $laporanTerbaru])

        <div class="pl-table-footer">
            <span class="pl-table-count">{{ $laporanTerbaru->count() }} laporan terbaru</span>
            <a href="{{ route('pelapor.laporan.index') }}" class="pl-link-muted">Lihat semua laporan &rarr;</a>
        </div>
    </div>

    <div class="pl-scope-note">
        SIPAFKA hanya melayani pelaporan fasilitas di lingkungan FMIPA Universitas Halu Oleo.
    </div>
@endsection
