@php
    $user = auth()->user();
    $roleTitle = match ($user?->role) {
        'admin' => 'Admin',
        'teknisi' => 'Teknisi',
        default => 'Pelapor',
    };
@endphp

<header class="topbar">
    <div class="topbar-left">
        <button type="button" class="topbar-menu-btn" id="sidebarToggle" aria-label="Buka menu navigasi">
            <span></span>
            <span></span>
            <span></span>
        </button>
        <span class="topbar-app-name">Sistem Informasi Pelaporan Fasilitas Kampus</span>
    </div>

    <div class="topbar-user">
        <span class="topbar-user-name">{{ $user?->name ?? 'Pengguna' }}</span>
        <span class="topbar-user-role">{{ $roleTitle }}</span>
    </div>
</header>
