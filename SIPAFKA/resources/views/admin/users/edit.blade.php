@extends('layouts.app')

@section('title', 'Ubah pengguna')
@section('breadcrumb', 'SIPAFKA / Admin / Kelola pengguna / Ubah')
@section('judul', 'Ubah data pengguna')
@section('subjudul', 'Perbarui identitas, peran, kata sandi baru, atau status aktif pengguna.')

@push('styles')
    <style>
        .adm-edit-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.6fr) minmax(260px, 1fr);
            gap: 18px;
            align-items: start;
        }

        .adm-card-title {
            margin: 0 0 4px;
            font-size: 16px;
            font-weight: 700;
            color: var(--sip-text);
        }

        .adm-card-sub {
            margin: 0 0 18px;
            font-size: 12px;
            color: var(--sip-text-subtle);
        }

        .adm-field-hint {
            margin-top: 5px;
            font-size: 11.5px;
            color: var(--sip-text-subtle);
        }

        .adm-form-actions {
            margin-top: 20px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
        }

        .adm-meta-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
            margin-top: 12px;
        }

        .adm-meta-label {
            font-size: 11px;
            color: var(--sip-text-subtle);
            margin-bottom: 3px;
        }

        .adm-meta-value {
            font-size: 13px;
            font-weight: 600;
            color: var(--sip-text);
            word-break: break-word;
        }

        .adm-role-badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
        }

        .adm-role-pelapor {
            background-color: #EEF2FF;
            color: #3730A3;
        }

        .adm-role-teknisi {
            background-color: #DBEAFE;
            color: #1D4ED8;
        }

        .adm-role-admin {
            background-color: #E2E8F0;
            color: #0F2D52;
        }

        @media (max-width: 960px) {
            .adm-edit-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')
    @php
        $roleLabel = $daftarRole[$user->role] ?? ucfirst($user->role);
        $roleClass = match ($user->role) {
            'admin' => 'adm-role-admin',
            'teknisi' => 'adm-role-teknisi',
            default => 'adm-role-pelapor',
        };
    @endphp

    <div class="adm-edit-grid">
        <section class="card">
            <h2 class="adm-card-title">Informasi pengguna</h2>
            <p class="adm-card-sub">Kosongkan kolom kata sandi baru jika tidak ingin mengganti kata sandi.</p>

            <form action="{{ route('admin.users.update', $user) }}" method="POST" novalidate>
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="name" class="form-label">Nama lengkap *</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', $user->name) }}"
                        placeholder="Nama pengguna"
                        maxlength="120"
                        required
                        class="form-control @error('name') is-invalid @enderror"
                    >
                    @error('name')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email *</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email', $user->email) }}"
                        placeholder="nama@uho.ac.id"
                        maxlength="150"
                        required
                        class="form-control @error('email') is-invalid @enderror"
                    >
                    @error('email')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="role" class="form-label">Peran pengguna *</label>
                    <select
                        id="role"
                        name="role"
                        required
                        class="form-control @error('role') is-invalid @enderror"
                    >
                        @foreach ($daftarRole as $kodeRole => $labelRole)
                            <option value="{{ $kodeRole }}" @selected(old('role', $user->role) === $kodeRole)>
                                {{ $labelRole }}
                            </option>
                        @endforeach
                    </select>
                    @error('role')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Kata sandi baru</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Kosongkan jika tidak ingin mengubah kata sandi"
                        class="form-control @error('password') is-invalid @enderror"
                    >
                    <div class="adm-field-hint">Opsional. Minimal 8 karakter jika diisi.</div>
                    @error('password')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="aktif" class="form-label">Status akun</label>
                    <select
                        id="aktif"
                        name="aktif"
                        class="form-control @error('aktif') is-invalid @enderror"
                    >
                        <option value="1" @selected((string) old('aktif', $user->aktif ? '1' : '0') === '1')>
                            Aktif (dapat login ke SIPAFKA)
                        </option>
                        <option value="0" @selected((string) old('aktif', $user->aktif ? '1' : '0') === '0')>
                            Nonaktif (akses login dinonaktifkan)
                        </option>
                    </select>
                    @error('aktif')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="adm-form-actions">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan perubahan</button>
                </div>
            </form>
        </section>

        <aside class="card">
            <h2 class="adm-card-title">Ringkasan akun</h2>
            <div class="adm-meta-list">
                <div>
                    <div class="adm-meta-label">Nama saat ini</div>
                    <div class="adm-meta-value">{{ $user->name }}</div>
                </div>
                <div>
                    <div class="adm-meta-label">Email terdaftar</div>
                    <div class="adm-meta-value">{{ $user->email }}</div>
                </div>
                <div>
                    <div class="adm-meta-label">Peran saat ini</div>
                    <div>
                        <span class="adm-role-badge {{ $roleClass }}">{{ $roleLabel }}</span>
                    </div>
                </div>
                <div>
                    <div class="adm-meta-label">Status akun</div>
                    <div class="adm-meta-value">{{ $user->aktif ? 'Aktif' : 'Nonaktif' }}</div>
                </div>
            </div>

            <div class="info-banner" style="margin-top: 18px;">
                <div style="font-size: 12px; font-weight: 700; color: var(--sip-primary); margin-bottom: 4px;">
                    Catatan integritas data
                </div>
                <p style="margin: 0; font-size: 12px; color: var(--sip-text-secondary);">
                    Akun pengguna tidak dihapus permanen agar riwayat laporan dan penugasan tetap terjaga. Gunakan status Nonaktif jika akses akun perlu dihentikan.
                </p>
            </div>
        </aside>
    </div>
@endsection
