@props(['status'])

@php
    $enum = $status instanceof \App\Enums\StatusLaporan
        ? $status
        : \App\Enums\StatusLaporan::tryFrom((string) $status);
    $label = $enum?->value ?? (string) $status;
    $class = $enum?->badgeClass() ?? 'badge-menunggu';
@endphp

<span {{ $attributes->merge(['class' => 'status-badge ' . $class]) }}>
    {{ $label }}
</span>
