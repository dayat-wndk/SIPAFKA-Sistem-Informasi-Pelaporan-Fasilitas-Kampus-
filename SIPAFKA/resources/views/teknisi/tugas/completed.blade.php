@extends('layouts.app')

@section('title', $laporan->nomor_laporan . ' — Perbaikan selesai')
@section('breadcrumb', 'SIPAFKA / Teknisi / Perbaikan selesai')
@section('judul', 'Perbaikan selesai')
@section('subjudul', 'Pembaruan akhir telah tersimpan pada laporan.')

@push('styles')
    <style>
        .tk-completed-card {
            padding: 26px 28px;
        }

        .tk-completed-heading {
            margin: 14px 0 6px;
            font-size: 20px;
            font-weight: 700;
            color: var(--sip-text);
        }

        .tk-completed-meta {
            font-size: 13px;
            color: var(--sip-text-muted);
            margin-bottom: 18px;
        }

        .tk-final-note-box {
            padding: 16px 18px;
            border-radius: var(--sip-radius-md);
            background-color: #ECFDF5;
            border: 1px solid #A7F3D0;
            margin-bottom: 16px;
        }

        .tk-final-note-title {
            font-size: 12px;
            font-weight: 700;
            color: #065F46;
            margin-bottom: 6px;
        }

        .tk-final-note-text {
            margin: 0;
            font-size: 13px;
            line-height: 1.55;
            color: #064E3B;
        }

        .tk-completed-by {
            font-size: 12.5px;
            font-weight: 500;
            color: var(--sip-text-secondary);
        }

        .tk-completed-info {
            margin: 6px 0 20px;
            font-size: 12px;
            color: var(--sip-text-subtle);
        }
    </style>
@endpush

@section('content')
    <section class="card tk-completed-card">
        <x-status-badge :status="$laporan->status" />

        <h2 class="tk-completed-heading">Perbaikan berhasil diselesaikan.</h2>

        <div class="tk-completed-meta">
            {{ $laporan->nomor_laporan }} &bull;
            {{ $laporan->judul }},
            {{ $laporan->fasilitas?->ruang ?? '-' }}@if ($laporan->fasilitas?->gedung), {{ $laporan->fasilitas->gedung }}@endif
        </div>

        <div class="tk-final-note-box">
            <div class="tk-final-note-title">Catatan akhir</div>
            <p class="tk-final-note-text">
                {{ $perbaikanSelesai?->hasil ?: $perbaikanSelesai?->catatan_progres ?: 'Perbaikan fasilitas telah selesai dan berfungsi normal.' }}
            </p>
        </div>

        <div class="tk-completed-by">
            {{ $laporan->penugasan?->teknisi?->name ?? auth()->user()->name }}
            &bull;
            {{ ($perbaikanSelesai?->tanggal ?? $laporan->updated_at)?->translatedFormat('d F Y, H.i') }}
        </div>

        <p class="tk-completed-info">
            Status dan catatan penyelesaian dapat dipantau oleh pelapor serta admin.
        </p>

        <a href="{{ route('teknisi.tugas.index') }}" class="btn btn-primary">
            Kembali ke daftar tugas
        </a>
    </section>
@endsection
