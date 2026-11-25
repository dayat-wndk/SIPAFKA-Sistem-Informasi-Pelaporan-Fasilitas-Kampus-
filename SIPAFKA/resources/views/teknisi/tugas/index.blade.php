@extends('layouts.app')

@section('title', 'Daftar tugas perbaikan')
@section('breadcrumb', 'SIPAFKA / Teknisi / Daftar tugas perbaikan')
@section('judul', 'Daftar tugas perbaikan')
@section('subjudul', 'Semua penugasan perbaikan yang menjadi tanggung jawab Anda.')

@push('styles')
    <style>
        .tk-filter-bar {
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 14px;
            margin-bottom: 14px;
        }

        .tk-filter-group {
            margin-bottom: 0;
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

        .tk-pagination {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .tk-page-links {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .tk-page-btn {
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

        .tk-page-btn.is-active {
            background-color: var(--sip-primary);
            border-color: var(--sip-primary);
            color: #FFFFFF;
        }

        .tk-page-btn.is-disabled {
            color: var(--sip-text-subtle);
            background-color: var(--sip-surface-muted);
        }

        @media (max-width: 640px) {
            .tk-filter-bar {
                grid-template-columns: 1fr;
            }

            .tk-table-footer {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
@endpush

@section('content')
    <form
        action="{{ route('teknisi.tugas.index') }}"
        method="GET"
        class="tk-filter-bar"
        id="formFilterTugas"
    >
        <div class="form-group tk-filter-group">
            <label for="inputCariTugas" class="form-label">Cari tugas</label>
            <input
                type="text"
                id="inputCariTugas"
                name="q"
                value="{{ $q }}"
                placeholder="Cari nomor atau kerusakan"
                class="form-control"
            >
        </div>

        <div class="form-group tk-filter-group">
            <label for="selectStatusTugas" class="form-label">Status tugas</label>
            <select id="selectStatusTugas" name="status" class="form-control">
                <option value="">Semua status</option>
                @foreach ($allowedStatuses as $st)
                    <option value="{{ $st->value }}" @selected($statusFilter === $st->value)>
                        {{ $st->value }}
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
                        <th>Laporan</th>
                        <th>Kerusakan &amp; Lokasi</th>
                        <th>Ditugaskan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tugasList as $item)
                        <tr>
                            <td class="tk-col-nomor">{{ $item->nomor_laporan }}</td>
                            <td>
                                <div class="tk-row-title">{{ $item->judul }}</div>
                                <div class="tk-row-sub">
                                    {{ $item->fasilitas?->ruang ?? '-' }}@if ($item->fasilitas?->gedung), {{ $item->fasilitas->gedung }}@endif
                                </div>
                            </td>
                            <td class="tk-col-date">
                                {{ ($item->penugasan?->tanggal_tugas ?? $item->tanggal)?->translatedFormat('d M Y') }}
                            </td>
                            <td>
                                <x-status-badge :status="$item->status" />
                            </td>
                            <td>
                                <a href="{{ route('teknisi.tugas.show', $item) }}" class="tk-action-link">
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
            <span>
                @if ($tugasList->total() > 0)
                    Menampilkan {{ $tugasList->firstItem() }}&ndash;{{ $tugasList->lastItem() }} dari {{ $tugasList->total() }} tugas
                @else
                    Menampilkan 0 tugas
                @endif
            </span>

            <div class="tk-pagination">
                <span>Halaman {{ $tugasList->currentPage() }} dari {{ $tugasList->lastPage() }}</span>

                @if ($tugasList->hasPages())
                    <div class="tk-page-links">
                        @if ($tugasList->onFirstPage())
                            <span class="tk-page-btn is-disabled">&lsaquo;</span>
                        @else
                            <a href="{{ $tugasList->previousPageUrl() }}" class="tk-page-btn" aria-label="Halaman sebelumnya">&lsaquo;</a>
                        @endif

                        @foreach ($tugasList->getUrlRange(1, $tugasList->lastPage()) as $page => $url)
                            @if ($page === $tugasList->currentPage())
                                <span class="tk-page-btn is-active">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="tk-page-btn">{{ $page }}</a>
                            @endif
                        @endforeach

                        @if ($tugasList->hasMorePages())
                            <a href="{{ $tugasList->nextPageUrl() }}" class="tk-page-btn" aria-label="Halaman berikutnya">&rsaquo;</a>
                        @else
                            <span class="tk-page-btn is-disabled">&rsaquo;</span>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @if (file_exists(resource_path('js/teknisi-progres.js')))
        <script>{!! file_get_contents(resource_path('js/teknisi-progres.js')) !!}</script>
    @endif
@endpush
