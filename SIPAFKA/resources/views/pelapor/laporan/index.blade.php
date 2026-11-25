@extends('layouts.app')

@section('title', 'Riwayat laporan')
@section('breadcrumb', 'SIPAFKA / Pelapor / Riwayat laporan')
@section('judul', 'Riwayat laporan')
@section('subjudul', 'Seluruh laporan kerusakan yang pernah Anda kirim.')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pelapor.css') }}">
@endpush

@section('content')
    <form
        action="{{ route('pelapor.laporan.index') }}"
        method="GET"
        class="pl-filter-bar"
        id="formFilterRiwayat"
    >
        <div class="form-group pl-filter-group">
            <label for="inputCari" class="form-label">Cari laporan</label>
            <input
                type="text"
                id="inputCari"
                name="cari"
                value="{{ $cari }}"
                placeholder="Cari judul atau nomor laporan (mis. SIP-001)"
                class="form-control"
            >
        </div>

        <div class="form-group pl-filter-group">
            <label for="selectStatus" class="form-label">Status laporan</label>
            <select id="selectStatus" name="status" class="form-control">
                <option value="">Semua status</option>
                @foreach ($daftarStatus as $st)
                    <option value="{{ $st->value }}" @selected($statusFilter === $st->value)>
                        {{ $st->value }}
                    </option>
                @endforeach
            </select>
        </div>
    </form>

    <div class="card-table">
        @include('pelapor.laporan._tabel', ['laporanList' => $laporanList])

        <div class="pl-table-footer">
            <span class="pl-table-count">
                @if ($laporanList->total() > 0)
                    Menampilkan {{ $laporanList->firstItem() }}&ndash;{{ $laporanList->lastItem() }} dari {{ $laporanList->total() }} laporan
                @else
                    Menampilkan 0 laporan
                @endif
            </span>

            <div class="pl-pagination">
                <span class="pl-page-info">
                    Halaman {{ $laporanList->currentPage() }} dari {{ $laporanList->lastPage() }}
                </span>

                @if ($laporanList->hasPages())
                    <div class="pl-page-links">
                        @if ($laporanList->onFirstPage())
                            <span class="pl-page-btn is-disabled">&lsaquo;</span>
                        @else
                            <a href="{{ $laporanList->previousPageUrl() }}" class="pl-page-btn">&lsaquo;</a>
                        @endif

                        @if ($laporanList->hasMorePages())
                            <a href="{{ $laporanList->nextPageUrl() }}" class="pl-page-btn">&rsaquo;</a>
                        @else
                            <span class="pl-page-btn is-disabled">&rsaquo;</span>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="pl-status-legend" aria-label="Keterangan status laporan">
        @foreach ($daftarStatus as $st)
            <x-status-badge :status="$st" />
        @endforeach
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/pelapor/laporan-index.js') }}"></script>
@endpush
