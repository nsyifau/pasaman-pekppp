@props(['variant' => 'info', 'title' => null, 'solid' => false])

@php
    $icons = [
        'info' => 'info',
        'success' => 'check-circle',
        'warning' => 'alert-triangle',
        'danger' => 'alert-circle',
    ];
@endphp

<div {{ $attributes->class(['alert', 'alert-' . $variant, 'alert-solid' => $solid]) }}
    @if ($variant === 'danger') role="alert" @endif>
    <x-icon :name="$icons[$variant] ?? 'info'" class="alert-icon mt-0.5 h-4 w-4 shrink-0" />
    <div class="min-w-0 flex-1">
        @if ($title)
            <p class="alert-title">{{ $title }}</p>
        @endif
        <div class="{{ $title ? 'mt-0.5' : '' }} [&_a]:font-medium [&_a]:underline">{{ $slot }}</div>
    </div>
    {{ $action ?? '' }}
</div>
