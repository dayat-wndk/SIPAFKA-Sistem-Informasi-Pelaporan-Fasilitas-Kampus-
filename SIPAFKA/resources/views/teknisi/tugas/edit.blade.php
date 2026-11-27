@extends('layouts.app')

@section('title', $laporan->nomor_laporan . ' — Perbarui status perbaikan')
@section('breadcrumb', 'SIPAFKA / Teknisi / Perbarui status perbaikan')
@section('judul', 'Perbarui status perbaikan')
@section('subjudul', 'Catat pekerjaan yang dilakukan dan kondisi terkini.')

@push('styles')
    <style>
        .tk-edit-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            margin-bottom: 18px;
            flex-wrap: wrap;
        }

        .tk-edit-title {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            color: var(--sip-text);
        }

        .tk-edit-sub {
            margin: 3px 0 0;
            font-size: 12.5px;
            color: var(--sip-text-muted);
        }

        .tk-edit-grid {
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

        .tk-card-sub {
            margin: 0 0 16px;
            font-size: 12px;
            color: var(--sip-text-muted);
        }

        .tk-field-hint {
            margin-top: 5px;
            font-size: 11.5px;
            color: var(--sip-text-subtle);
            display: flex;
            justify-content: space-between;
            gap: 8px;
        }

        .tk-warning-banner {
            margin-top: 16px;
            font-size: 12.5px;
            color: var(--sip-text-secondary);
            transition: background-color 0.15s ease, border-color 0.15s ease;
        }

        .tk-warning-banner.is-selesai-warn {
            background-color: #ECFDF5;
            border-color: #A7F3D0;
            color: #065F46;
        }

        .tk-form-actions {
            margin-top: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .tk-history-list {
            list-style: none;
            margin: 14px 0 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .tk-history-item {
            padding-bottom: 14px;
            border-bottom: 1px solid #F1F5F9;
        }

        .tk-history-item:last-child {
            padding-bottom: 0;
            border-bottom: none;
        }

        .tk-history-time {
            font-size: 11.5px;
            font-weight: 600;
            color: var(--sip-text-subtle);
            margin-bottom: 4px;
        }

        .tk-history-text {
            font-size: 13px;
            line-height: 1.5;
            color: var(--sip-text-secondary);
        }

        .tk-client-error {
            display: none;
            margin-top: 5px;
            font-size: 12px;
            color: #DC2626;
        }

        .tk-client-error.is-visible {
            display: block;
        }

        @media (max-width: 960px) {
            .tk-edit-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')
    <div class="tk-edit-head">
        <div>
            <h2 class="tk-edit-title">
                {{ $laporan->nomor_laporan }} &bull; {{ $laporan->fasilitas?->ruang ?? $laporan->judul }}
            </h2>
            <p class="tk-edit-sub">
                {{ $laporan->judul }}
                @if ($laporan->fasilitas?->gedung)
                    &bull; {{ $laporan->fasilitas->gedung }}
                @endif
            </p>
        </div>

        <x-status-badge :status="$laporan->status" />
    </div>

    <div class="tk-edit-grid">
        <section class="card">
            <h3 class="tk-card-title">Perkembangan pekerjaan</h3>
            <p class="tk-card-sub">
                Status saat ini: {{ $laporan->status->value }}. Lengkapi catatan sebelum menyimpan.
            </p>

            <form
                action="{{ route('teknisi.tugas.update', $laporan) }}"
                method="POST"
                id="formUpdateProgres"
                novalidate
            >
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="status" class="form-label">Status laporan *</label>
                    <select
                        id="status"
                        name="status"
                        required
                        class="form-control @error('status') is-invalid @enderror"
                    >
                        <option value="Diproses" @selected(old('status', $laporan->status->value) === 'Diproses')>
                            Diproses
                        </option>
                        <option value="Selesai" @selected(old('status', $laporan->status->value) === 'Selesai')>
                            Selesai
                        </option>
                    </select>
                    <div class="tk-field-hint">
                        <span>Pilihan status teknisi: Diproses atau Selesai.</span>
                    </div>
                    @error('status')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="catatan" class="form-label">Catatan perkembangan *</label>
                    <textarea
                        id="catatan"
                        name="catatan"
                        rows="4"
                        required
                        minlength="10"
                        maxlength="2000"
                        placeholder="Tuliskan perkembangan pekerjaan atau hasil akhir pengujian fasilitas..."
                        class="form-control @error('catatan') is-invalid @enderror"
                    >{{ old('catatan') }}</textarea>
                    <div class="tk-field-hint">
                        <span>Minimal 10 karakter.</span>
                        <span id="catatanCharCount">{{ mb_strlen((string) old('catatan', '')) }} karakter</span>
                    </div>
                    <div class="tk-client-error" id="catatanClientError" role="alert"></div>
                    @error('catatan')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="info-banner tk-warning-banner" id="bannerPeringatanSelesai">
                    Catatan akan terlihat pada detail laporan pelapor dan admin. Pilih Selesai hanya setelah fasilitas berfungsi kembali.
                </div>

                <div class="tk-form-actions">
                    <a href="{{ route('teknisi.tugas.show', $laporan) }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary" id="btnSimpanProgres">Simpan pembaruan</button>
                </div>
            </form>
        </section>

        <aside class="card">
            <h3 class="tk-card-title">Catatan sebelumnya</h3>

            <ul class="tk-history-list">
                @forelse ($riwayatPerbaikan as $item)
                    <li class="tk-history-item">
                        <div class="tk-history-time">
                            {{ $item->tanggal?->translatedFormat('d M Y • H.i') }}
                        </div>
                        <div class="tk-history-text">
                            {{ $item->hasil ?: $item->catatan_progres }}
                        </div>
                    </li>
                @empty
                    <li class="tk-history-item">
                        <div class="tk-history-time">
                            {{ $laporan->penugasan?->tanggal_tugas?->translatedFormat('d M Y • H.i') ?? '-' }}
                        </div>
                        <div class="tk-history-text">
                            Penugasan diterima dari {{ $laporan->penugasan?->admin?->name ?? 'Admin FMIPA' }}.
                        </div>
                    </li>
                @endforelse
            </ul>
        </aside>
    </div>
@endsection

@push('scripts')
    @if (file_exists(resource_path('js/teknisi-progres.js')))
        <script>{!! file_get_contents(resource_path('js/teknisi-progres.js')) !!}</script>
    @endif
@endpush
