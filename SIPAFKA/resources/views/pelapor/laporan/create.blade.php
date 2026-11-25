@extends('layouts.app')

@section('title', 'Buat laporan kerusakan')
@section('breadcrumb', 'SIPAFKA / Pelapor / Buat laporan kerusakan')
@section('judul', 'Buat laporan kerusakan')
@section('subjudul', 'Sampaikan kerusakan fasilitas di lingkungan FMIPA UHO.')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pelapor.css') }}">
@endpush

@section('content')
    <div class="pl-create-grid">
        <section class="card">
            <h2 class="pl-card-title">Informasi kerusakan</h2>
            <p class="pl-card-sub">Lengkapi kolom bertanda * dengan informasi yang jelas.</p>

            <form
                action="{{ route('pelapor.laporan.store') }}"
                method="POST"
                enctype="multipart/form-data"
                id="formBuatLaporan"
                novalidate
            >
                @csrf

                <div class="form-group">
                    <label for="judul" class="form-label">Judul laporan *</label>
                    <input
                        type="text"
                        id="judul"
                        name="judul"
                        maxlength="150"
                        value="{{ old('judul') }}"
                        placeholder="Contoh: Proyektor tidak menyala"
                        class="form-control @error('judul') is-invalid @enderror"
                        required
                    >
                    @error('judul')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="deskripsi" class="form-label">Deskripsi kerusakan *</label>
                    <textarea
                        id="deskripsi"
                        name="deskripsi"
                        rows="4"
                        placeholder="Jelaskan kondisi kerusakan secara ringkas dan jelas..."
                        class="form-control @error('deskripsi') is-invalid @enderror"
                        required
                    >{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="pl-form-row">
                    <div class="form-group">
                        <label for="fasilitasNama" class="form-label">Fasilitas *</label>
                        <select
                            id="fasilitasNama"
                            name="fasilitas_nama"
                            class="form-control"
                        >
                            <option value="">Pilih fasilitas</option>
                            @foreach ($namaFasilitasList as $nama)
                                <option value="{{ $nama }}" @selected($oldFasilitasNama === $nama)>
                                    {{ $nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="fasilitasLokasi" class="form-label">Lokasi *</label>
                        <select
                            id="fasilitasLokasi"
                            name="fasilitas_id"
                            data-old-id="{{ $oldFasilitasId }}"
                            class="form-control @error('fasilitas_id') is-invalid @enderror"
                            required
                        >
                            <option value="">Pilih fasilitas terlebih dahulu</option>
                        </select>
                        @error('fasilitas_id')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="foto" class="form-label">Foto kerusakan *</label>
                    <div class="pl-upload-box @error('foto') is-invalid @enderror" id="fotoUploadBox">
                        <input
                            type="file"
                            id="foto"
                            name="foto"
                            accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                            class="pl-file-input"
                            required
                        >
                        <div class="pl-upload-info" id="fotoPreview">
                            <span class="pl-upload-placeholder">Pilih file foto kerusakan (JPG atau PNG, maksimal 5 MB)</span>
                        </div>
                    </div>
                    <div class="pl-upload-hint">Foto wajib diunggah. Format JPG atau PNG, maksimal 5 MB.</div>
                    <div class="form-error pl-hidden" id="fotoClientError"></div>
                    @error('foto')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="pl-form-actions">
                    <a href="{{ route('pelapor.dashboard') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Kirim laporan</button>
                </div>
            </form>
        </section>

        <aside class="card pl-guide-card">
            <h2 class="pl-card-title">Sebelum mengirim</h2>

            <ol class="pl-guide-list">
                <li>
                    <span class="pl-guide-num">01</span>
                    <span>Pastikan lokasi berada di FMIPA UHO.</span>
                </li>
                <li>
                    <span class="pl-guide-num">02</span>
                    <span>Pilih fasilitas sesuai kerusakan.</span>
                </li>
                <li>
                    <span class="pl-guide-num">03</span>
                    <span>Sertakan foto yang jelas.</span>
                </li>
                <li>
                    <span class="pl-guide-num">04</span>
                    <span>Pantau status melalui riwayat laporan.</span>
                </li>
            </ol>

            <div class="info-banner pl-guide-note">
                <div class="pl-guide-note-title">Apa langkah berikutnya?</div>
                <p class="pl-guide-note-text">Admin memverifikasi laporan sebelum menugaskan teknisi.</p>
            </div>
        </aside>
    </div>

    <script id="fasilitasDataJson" type="application/json">@json($fasilitasJson)</script>
@endsection

@push('scripts')
    <script src="{{ asset('js/pelapor/laporan-create.js') }}"></script>
@endpush
