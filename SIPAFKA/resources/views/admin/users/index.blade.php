@extends('layouts.app')

@section('title', 'Kelola pengguna')
@section('breadcrumb', 'SIPAFKA / Admin / Kelola pengguna')
@section('judul', 'Kelola pengguna')
@section('subjudul', 'Atur akses pelapor, admin, dan teknisi.')

@push('styles')
    <style>
        .adm-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.55fr) minmax(290px, 1fr);
            gap: 18px;
            align-items: start;
        }

        .adm-panel-card {
            padding: 0;
            overflow: hidden;
        }

        .adm-search-box {
            padding: 18px 20px 16px;
            border-bottom: 1px solid var(--sip-border);
        }

        .adm-search-row {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .adm-section-head {
            padding: 16px 20px 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .adm-section-title {
            margin: 0;
            font-size: 15px;
            font-weight: 700;
            color: var(--sip-text);
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

        .adm-name-cell {
            font-weight: 600;
            color: var(--sip-text);
        }

        .adm-email-cell {
            color: var(--sip-text-muted);
            word-break: break-word;
        }

        .adm-role-badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            line-height: 1.2;
            white-space: nowrap;
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

        .adm-inactive-tag {
            display: inline-block;
            margin-left: 6px;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 600;
            background-color: #FEE2E2;
            color: #B91C1C;
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
            padding: 28px 18px !important;
            color: var(--sip-text-muted);
        }

        .adm-table-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 12px 20px;
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
            width: 28px;
            height: 28px;
            border-radius: 6px;
            border: 1px solid var(--sip-border);
            background-color: var(--sip-surface);
            color: var(--sip-text);
            font-weight: 600;
        }

        .adm-page-btn.is-disabled {
            color: var(--sip-text-subtle);
            background-color: var(--sip-surface-muted);
        }

        .adm-form-note {
            margin: 12px 0 0;
            font-size: 11.5px;
            color: var(--sip-text-subtle);
        }

        @media (max-width: 960px) {
            .adm-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .adm-table-footer {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
@endpush

@section('content')
    <div class="adm-grid">
        <section class="card adm-panel-card">
            <div class="adm-search-box">
                <form action="{{ route('admin.users.index') }}" method="GET" id="formCariPengguna">
                    <label for="inputCariPengguna" class="form-label">Pencarian</label>
                    <div class="adm-search-row">
                        <input
                            type="text"
                            id="inputCariPengguna"
                            name="cari"
                            value="{{ $cari }}"
                            placeholder="Cari nama atau email"
                            class="form-control"
                        >
                        @if ($cari !== '')
                            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Reset</a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="adm-section-head">
                <h2 class="adm-section-title">Pengguna terdaftar</h2>
            </div>

            <div class="table-wrap">
                <table class="sip-table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $item)
                            @php
                                $roleLabel = $daftarRole[$item->role] ?? ucfirst($item->role);
                                $roleClass = match ($item->role) {
                                    'admin' => 'adm-role-admin',
                                    'teknisi' => 'adm-role-teknisi',
                                    default => 'adm-role-pelapor',
                                };
                            @endphp
                            <tr>
                                <td class="adm-name-cell">
                                    {{ $item->name }}
                                    @if (! $item->aktif)
                                        <span class="adm-inactive-tag">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="adm-email-cell">{{ $item->email }}</td>
                                <td>
                                    <span class="adm-role-badge {{ $roleClass }}">{{ $roleLabel }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.users.edit', $item) }}" class="adm-action-link">Ubah</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="adm-empty-cell">
                                    Belum ada data pengguna yang sesuai.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="adm-table-footer">
                <span>
                    @if ($users->total() > 0)
                        Menampilkan {{ $users->firstItem() }}&ndash;{{ $users->lastItem() }} dari {{ $users->total() }} data
                    @else
                        Menampilkan 0 data
                    @endif
                </span>

                <div class="adm-pagination">
                    <span>Halaman {{ $users->currentPage() }} dari {{ $users->lastPage() }}</span>
                    @if ($users->hasPages())
                        <div class="adm-page-links">
                            @if ($users->onFirstPage())
                                <span class="adm-page-btn is-disabled">&lsaquo;</span>
                            @else
                                <a href="{{ $users->previousPageUrl() }}" class="adm-page-btn" aria-label="Halaman sebelumnya">&lsaquo;</a>
                            @endif

                            @if ($users->hasMorePages())
                                <a href="{{ $users->nextPageUrl() }}" class="adm-page-btn" aria-label="Halaman berikutnya">&rsaquo;</a>
                            @else
                                <span class="adm-page-btn is-disabled">&rsaquo;</span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </section>

        <aside class="card">
            <h2 class="adm-card-title">Tambah pengguna</h2>
            <p class="adm-card-sub">Lengkapi data bertanda *.</p>

            <form action="{{ route('admin.users.store') }}" method="POST" novalidate>
                @csrf

                <div class="form-group">
                    <label for="name" class="form-label">Nama lengkap *</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
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
                        value="{{ old('email') }}"
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
                        <option value="">Pilih peran</option>
                        @foreach ($daftarRole as $kodeRole => $labelRole)
                            <option value="{{ $kodeRole }}" @selected(old('role') === $kodeRole)>
                                {{ $labelRole }}
                            </option>
                        @endforeach
                    </select>
                    @error('role')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Kata sandi awal *</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Minimal 8 karakter"
                        minlength="8"
                        required
                        class="form-control @error('password') is-invalid @enderror"
                    >
                    @error('password')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Simpan data</button>

                <p class="adm-form-note">Hak akses disesuaikan dengan peran pengguna.</p>
            </form>
        </aside>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('formCariPengguna');
            const input = document.getElementById('inputCariPengguna');
            if (!form || !input) return;

            let timer = null;
            input.addEventListener('input', function () {
                clearTimeout(timer);
                timer = setTimeout(function () {
                    form.submit();
                }, 400);
            });
        });
    </script>
@endpush
