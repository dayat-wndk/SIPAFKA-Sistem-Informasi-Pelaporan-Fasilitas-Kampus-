@props([
    'href' => '#',
    'nomor' => '01',
    'label' => '',
    'active' => false,
])

<a href="{{ $href }}" class="sidebar-nav-item {{ $active ? 'is-active' : '' }}">
    <span class="sidebar-nav-num">{{ $nomor }}</span>
    <span class="sidebar-nav-label">{{ $label }}</span>
</a>
