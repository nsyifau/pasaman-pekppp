@props(['variant' => 'primary', 'size' => 'default', 'type' => 'button'])
@php
    $variants = [
        'primary' => 'btn-primary', 'secondary' => 'btn-secondary', 'outline' => 'btn-outline',
        'ghost' => 'btn-ghost', 'soft' => 'btn-soft', 'destructive' => 'btn-destructive',
        'success' => 'btn-success', 'warning' => 'btn-warning', 'info' => 'btn-info', 'link' => 'btn-link',
    ];
    $sizes = ['default' => '', 'sm' => 'btn-sm', 'lg' => 'btn-lg', 'icon' => 'btn-icon'];
@endphp
<button type="{{ $type }}" {{ $attributes->class(['btn', $variants[$variant] ?? $variants['primary'], $sizes[$size] ?? '']) }}>
    {{ $slot }}
</button>

