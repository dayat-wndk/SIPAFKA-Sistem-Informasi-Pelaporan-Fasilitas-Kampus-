@php
    $user = auth()->user();
    $roleLabel = match ($user?->role) {
        'admin' => 'Ruang admin',
        'teknisi' => 'Ruang teknisi',
        default => 'Ruang pelapor',
    };
@endphp

<aside class="sidebar" id="appSidebar">
    <div class="sidebar-top">
        <div class="sidebar-brand">
            <div class="sidebar-logo">SIPAFKA</div>
            <div class="sidebar-brand-sub">FMIPA &bull; UNIVERSITAS HALU OLEO</div>
        </div>

        <div class="sidebar-role-pill">{{ $roleLabel }}</div>

        <nav class="sidebar-nav" aria-label="Menu utama">
            @if ($user && $user->role === 'pelapor')
                {{-- MENU MILIK HATMAN FIRDAYAT DIMULAI --}}
                <x-nav-item
                    href="{{ route('pelapor.dashboard') }}"
                    nomor="01"
                    label="Dashboard"
                    :active="request()->routeIs('pelapor.dashboard')"
                />
                <x-nav-item
                    href="{{ route('pelapor.laporan.create') }}"
                    nomor="02"
                    label="Buat laporan"
                    :active="request()->routeIs('pelapor.laporan.create')"
                />
                <x-nav-item
                    href="{{ route('pelapor.laporan.index') }}"
                    nomor="03"
                    label="Riwayat laporan"
                    :active="request()->routeIs('pelapor.laporan.index', 'pelapor.laporan.show', 'pelapor.laporan.sukses')"
                />
                {{-- MENU MILIK HATMAN FIRDAYAT BERAKHIR --}}
            @elseif ($user && $user->role === 'admin')
                <x-nav-item
                    href="{{ route('admin.dashboard') }}"
                    nomor="01"
                    label="Dashboard"
                    :active="request()->routeIs('admin.dashboard')"
                />
                <x-nav-item
                    href="{{ route('admin.laporan.index') }}"
                    nomor="02"
                    label="Laporan masuk"
                    :active="request()->routeIs('admin.laporan.*')"
                />
                <x-nav-item
                    href="{{ route('admin.users.index') }}"
                    nomor="03"
                    label="Kelola pengguna"
                    :active="request()->routeIs('admin.users.*')"
                />
                <x-nav-item
                    href="{{ route('admin.fasilitas.index') }}"
                    nomor="04"
                    label="Kelola fasilitas"
                    :active="request()->routeIs('admin.fasilitas.*')"
                />
            @elseif ($user && $user->role === 'teknisi')
                <x-nav-item
                    href="{{ route('teknisi.dashboard') }}"
                    nomor="01"
                    label="Dashboard"
                    :active="request()->routeIs('teknisi.dashboard')"
                />
                <x-nav-item
                    href="{{ route('teknisi.tugas.index') }}"
                    nomor="02"
                    label="Daftar tugas"
                    :active="request()->routeIs('teknisi.tugas.*')"
                />
            @endif
        </nav>
    </div>

    <div class="sidebar-footer">
        <div class="sidebar-footer-eyebrow">KHUSUS FMIPA UHO</div>
        <p class="sidebar-footer-desc">Fasilitas terjaga,<br>aktivitas kampus lancar.</p>

        <form action="{{ route('logout') }}" method="POST" class="sidebar-logout-form">
            @csrf
            <button type="submit" class="sidebar-logout-btn">
                <span>Keluar dari akun</span>
                <span aria-hidden="true">&rarr;</span>
            </button>
        </form>
    </div>
</aside>
