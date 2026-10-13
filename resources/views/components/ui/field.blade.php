@props(['label' => null, 'name' => null, 'help' => null, 'required' => false, 'class' => ''])

@php
    $id = $attributes->get('id', $name ? 'f-' . str_replace(['[', ']'], '-', $name) : null);
    $hasError = $name && $errors->has($name);
@endphp

<div {{ $attributes->only('class')->class(['space-y-1.5', $class]) }}>
    @if ($label)
        <label @if ($id) for="{{ $id }}" @endif class="label">
            {{ $label }}
            @if ($required)
                <span class="text-danger" aria-hidden="true">*</span>
            @endif
        </label>
    @endif

    {{ $slot }}

    @if ($hasError)
        <p class="field-error">{{ $errors->first($name) }}</p>
    @elseif ($help)
        <p class="field-help">{{ $help }}</p>
    @endif
</div>
