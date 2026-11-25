@props([
    'breadcrumb' => null,
    'judul' => '',
    'subjudul' => null,
])

<div class="page-header">
    <div class="page-header-text">
        @if ($breadcrumb)
            <div class="page-breadcrumb">{{ $breadcrumb }}</div>
        @endif
        <h1 class="page-title">{{ $judul }}</h1>
        @if ($subjudul)
            <p class="page-subtitle">{{ $subjudul }}</p>
        @endif
    </div>

    @isset($aksi_header)
        <div class="page-header-actions">
            {{ $aksi_header }}
        </div>
    @endisset
</div>
