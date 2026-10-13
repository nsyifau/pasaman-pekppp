@props(['variant' => 'neutral', 'pill' => false, 'dot' => false, 'icon' => null])

@php
    $map = [
        'primary' => 'badge-primary',
        'soft' => 'badge-soft',
        'success' => 'badge-success',
        'danger' => 'badge-danger',
        'warning' => 'badge-warning',
        'info' => 'badge-info',
        'neutral' => 'badge-neutral',
        'outline' => 'badge-outline',
        'solid-success' => 'badge-solid-success',
        'solid-danger' => 'badge-solid-danger',
        'solid-warning' => 'badge-solid-warning',
        'solid-info' => 'badge-solid-info',
    ];
@endphp

<span {{ $attributes->class(['badge', $map[$variant] ?? $map['neutral'], 'pill' => $pill, 'badge-dot' => $dot]) }}>
    @if ($icon)
        <x-icon :name="$icon" class="h-3 w-3" />
    @endif
    {{ $slot }}
</span>
