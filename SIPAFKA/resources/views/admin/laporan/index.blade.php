@extends('layouts.app')

@section('title', 'Laporan masuk')
@section('breadcrumb', 'SIPAFKA / Admin / Laporan masuk')
@section('judul', 'Laporan masuk')
@section('subjudul', 'Tinjau laporan dan tindak lanjuti sesuai statusnya.')

@push('styles')
<style>
    .adm-hidden { display: none; }
    .adm-banner-sukses {
        background-color: #ECFDF5;
        border: 1px solid #A7F3D0;
        border-radius: var(--sip-radius-md);
        padding: 16px 20px;
        margin-bottom: 18px;
    }
    .adm-banner-sukses-title {
        margin: 0;
        font-size: 18px;
        font-weight: 700;
        color: #065F46;
    }
    .adm-banner-sukses-sub {
        margin: 4px 0 0;
        font-size: 13px;
        color: #047857;
    }
    .adm-filter-bar {
        display: grid;
        grid-template-columns: 1.5fr 1fr 1fr;
        gap: 14px;
        margin-bottom: 16px;
    }
    .adm-filter-group {
        margin-bottom: 0;
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
        padding: 32px 18px !important;
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
    .adm-pagination {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .adm-page-links {
        display: inline-flex;
        gap: 6px;
    }
    .adm-page-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 28px;
        height: 28px;
        padding: 0 8px;
        border-radius: 6px;
        border: 1px solid var(--sip-border);
        background-color: var(--sip-surface);
        color: var(--sip-text);
        font-weight: 600;
        font-size: 12px;
    }
    .adm-page-btn.is-active {
        background-color: var(--sip-primary);
        border-color: var(--sip-primary);
        color: #FFFFFF;
    }
    .adm-page-btn.is-disabled {
        color: var(--sip-text-subtle);
        background-color: var(--sip-surface-muted);
    }
    .adm-status-legend {
        margin-top: 14px;
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }
    @media (max-width: 768px) {
        .adm-filter-bar {
            grid-template-columns: 1fr;
        }
        .adm-table-footer {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>
@endpush

@section('content')
    @if (session('banner_sukses'))
        @php $banner = session('banner_sukses'); @endphp
        <div class="adm-banner-sukses" role="status">
            <h2 class="adm-banner-sukses-title">{{ $banner['judul'] ?? 'Berhasil' }}</h2>
            <p class="adm-banner-sukses-sub">{{ $banner['pesan'] ?? '' }}</p>
        </div>
    @endif

    <form
        action="{{ route('admin.laporan.index') }}"
        method="GET"
        class="adm-filter-bar"
        id="formFilterAdminLaporan"
    >
        <div class="form-group adm-filter-group">
            <label for="inputCari" class="form-label">Cari laporan</label>
            <input
                type="text"
                id="inputCari"
                name="cari"
                value="{{ $cari }}"
                placeholder="Cari nomor, judul, atau pelapor"
                class="form-control"
            >
        </div>

        <div class="form-group adm-filter-group">
            <label for="selectStatus" class="form-label">Status</label>
            <select id="selectStatus" name="status" class="form-control">
                <option value="">Semua status</option>
                @foreach ($daftarStatus as $st)
                    <option value="{{ $st->value }}" @selected($statusFilter === $st->value)>
                        {{ $st->value }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group adm-filter-group">
            <label for="selectFasilitas" class="form-label">Fasilitas</label>
            <select id="selectFasilitas" name="fasilitas_id" class="form-control">
                <option value="">Semua fasilitas</option>
                @foreach ($daftarFasilitas as $fasilitas)
                    <option value="{{ $fasilitas->id }}" @selected((string) $fasilitasFilter === (string) $fasilitas->id)>
                        {{ $fasilitas->nama }} &mdash; {{ $fasilitas->ruang }}
                    </option>
                @endforeach
            </select>
        </div>
    </form>

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
                    @forelse ($laporanList as $item)
                        <tr>
                            <td class="adm-col-nomor">{{ $item->nomor_laporan }}</td>
                            <td>
                                <div class="adm-row-title">{{ $item->judul }}</div>
                                <div class="adm-row-sub">
                                    {{ $item->fasilitas?->ruang ?? '-' }}
                                    &bull;
                                    {{ $item->pelapor?->name ?? '-' }}
                                </div>
                            </td>
                            <td class="adm-col-date">{{ $item->tanggal?->translatedFormat('d M Y') }}</td>
                            <td>
                                <x-status-badge :status="$item->status" />
                            </td>
                            <td>
                                @if ($item->status === \App\Enums\StatusLaporan::Diverifikasi)
                                    <a href="{{ route('admin.laporan.assign', $item) }}" class="adm-action-link">
                                        Tugaskan &rarr;
                                    </a>
                                @elseif ($item->status === \App\Enums\StatusLaporan::Ditolak)
                                    <a href="{{ route('admin.laporan.rejected', $item) }}" class="adm-action-link">
                                        Tinjau &rarr;
                                    </a>
                                @else
                                    <a href="{{ route('admin.laporan.show', $item) }}" class="adm-action-link">
                                        Tinjau &rarr;
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="adm-empty-cell">
                                Tidak ada laporan masuk yang sesuai dengan filter saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="adm-table-footer">
            <span>
                @if ($laporanList->total() > 0)
                    Menampilkan {{ $laporanList->firstItem() }}&ndash;{{ $laporanList->lastItem() }} dari {{ $laporanList->total() }} laporan
                @else
                    Menampilkan 0 laporan
                @endif
            </span>

            <div class="adm-pagination">
                <span>Halaman {{ $laporanList->currentPage() }} dari {{ $laporanList->lastPage() }}</span>

                @if ($laporanList->hasPages())
                    <div class="adm-page-links">
                        @if ($laporanList->onFirstPage())
                            <span class="adm-page-btn is-disabled">&lsaquo;</span>
                        @else
                            <a href="{{ $laporanList->previousPageUrl() }}" class="adm-page-btn">&lsaquo;</a>
                        @endif

                        @foreach ($laporanList->getUrlRange(1, $laporanList->lastPage()) as $page => $url)
                            @if ($page === $laporanList->currentPage())
                                <span class="adm-page-btn is-active">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="adm-page-btn">{{ $page }}</a>
                            @endif
                        @endforeach

                        @if ($laporanList->hasMorePages())
                            <a href="{{ $laporanList->nextPageUrl() }}" class="adm-page-btn">&rsaquo;</a>
                        @else
                            <span class="adm-page-btn is-disabled">&rsaquo;</span>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="adm-status-legend" aria-label="Keterangan status laporan">
        @foreach ($daftarStatus as $st)
            <x-status-badge :status="$st" />
        @endforeach
    </div>
@endsection

@push('scripts')
    @if (file_exists(resource_path('js/admin-verifikasi.js')))
        <script>{!! file_get_contents(resource_path('js/admin-verifikasi.js')) !!}</script>
    @endif
@endpush
