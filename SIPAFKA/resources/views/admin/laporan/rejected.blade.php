@extends('layouts.app')

@section('title', $laporan->nomor_laporan . ' — Laporan ditolak')
@section('breadcrumb', 'SIPAFKA / Admin / Laporan ditolak')
@section('judul', 'Laporan ditolak')
@section('subjudul', 'Laporan tidak dapat diteruskan ke tahap penugasan.')

@push('styles')
<style>
    .adm-rejected-card {
        padding: 24px 26px;
    }
    .adm-rejected-title {
        margin: 14px 0 4px;
        font-size: 19px;
        font-weight: 700;
        color: var(--sip-text);
    }
    .adm-rejected-meta {
        margin: 0 0 18px;
        font-size: 12.5px;
        color: var(--sip-text-subtle);
    }
    .adm-reject-box {
        padding: 16px 18px;
        border-radius: var(--sip-radius-md);
        background-color: #FEF2F2;
        border: 1px solid #FECACA;
        margin-bottom: 18px;
    }
    .adm-reject-box-title {
        font-size: 13px;
        font-weight: 700;
        color: #B91C1C;
        margin-bottom: 6px;
    }
    .adm-reject-box-text {
        margin: 0;
        font-size: 13px;
        line-height: 1.55;
        color: #7F1D1D;
    }
    .adm-admin-note-label {
        font-size: 11.5px;
        color: var(--sip-text-subtle);
        margin-bottom: 4px;
    }
    .adm-admin-note-text {
        margin: 0 0 20px;
        font-size: 13px;
        color: var(--sip-text-secondary);
        line-height: 1.55;
    }
</style>
@endpush

@section('content')
    <div class="card adm-rejected-card">
        <x-status-badge :status="$laporan->status" />

        <h2 class="adm-rejected-title">{{ $laporan->nomor_laporan }} &bull; {{ $laporan->judul }}</h2>
        <p class="adm-rejected-meta">
            Pelapor: {{ $laporan->pelapor?->name ?? '-' }}
            &bull;
            {{ ($laporan->diverifikasi_at ?? $laporan->tanggal)?->translatedFormat('d F Y') }}
        </p>

        <div class="adm-reject-box" role="alert">
            <div class="adm-reject-box-title">Alasan penolakan</div>
            <p class="adm-reject-box-text">
                {{ $laporan->alasan_penolakan ?: 'Laporan tidak memenuhi kriteria verifikasi.' }}
            </p>
        </div>

        <div>
            <div class="adm-admin-note-label">Catatan admin</div>
            <p class="adm-admin-note-text">
                Ditolak oleh {{ $laporan->admin?->name ?? 'Admin FMIPA' }}
                @if ($laporan->diverifikasi_at)
                    pada {{ $laporan->diverifikasi_at->translatedFormat('d M Y, H.i') }}.
                @endif
                Silakan buat laporan baru dengan lokasi dan foto fasilitas yang sesuai.
            </p>
        </div>

        <a href="{{ route('admin.laporan.index') }}" class="btn btn-primary">
            Kembali ke laporan masuk
        </a>
    </div>
@endsection
