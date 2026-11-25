@extends('layouts.app')

@section('title', 'Laporan berhasil dikirim')
@section('breadcrumb', 'SIPAFKA / Pelapor / Laporan berhasil dikirim')
@section('judul', 'Laporan berhasil dikirim')
@section('subjudul', 'Laporan Anda tercatat dan menunggu verifikasi admin.')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pelapor.css') }}">
@endpush

@section('content')
    <section class="card pl-success-card">
        <h2 class="pl-success-heading">Laporan Anda sudah kami terima.</h2>
        <p class="pl-success-desc">
            Terima kasih telah membantu menjaga fasilitas FMIPA UHO. Simpan nomor laporan untuk memantau penanganannya.
        </p>

        <div class="pl-success-code-row">
            <span class="pl-success-code">{{ $laporan->nomor_laporan }}</span>
            <x-status-badge :status="$laporan->status" />
        </div>

        <div class="pl-success-title">{{ $laporan->judul }}</div>
        <div class="pl-success-meta">
            {{ $laporan->fasilitas?->nama ?? '-' }}
            &bull;
            {{ $laporan->fasilitas?->ruang ?? '-' }},
            {{ $laporan->fasilitas?->gedung ?? '-' }}
        </div>
        <div class="pl-success-time">
            {{ $laporan->created_at?->translatedFormat('d F Y, H.i') }}
        </div>

        <div class="info-banner pl-success-note">
            Admin akan memeriksa kesesuaian lokasi, deskripsi, dan foto kerusakan sebelum laporan diteruskan.
        </div>

        <div class="pl-success-actions">
            <a href="{{ route('pelapor.laporan.index') }}" class="btn btn-primary">Lihat riwayat laporan</a>
            <a href="{{ route('pelapor.dashboard') }}" class="btn btn-secondary">Kembali ke dashboard</a>
        </div>
    </section>
@endsection
