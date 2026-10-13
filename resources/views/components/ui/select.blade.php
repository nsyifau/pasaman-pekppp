@props(['name' => null, 'value' => null, 'placeholder' => null, 'invalid' => null, 'options' => [], 'includeBlank' => false, 'searchable' => false])

@php
    $id = $attributes->get('id', $name ? 'f-' . str_replace(['[', ']'], '-', $name) : null);
    $hasError = $invalid ?? ($name && $errors->has($name));
    $selected = old($name, $value);
@endphp

<select name="{{ $name }}" @if ($id) id="{{ $id }}" @endif @if ($hasError) aria-invalid="true" @endif
    @if ($searchable) data-enhanced-select data-placeholder="{{ $placeholder ?? 'Pilih pilihan' }}" @endif
    {{ $attributes->class(['select', 'input-invalid' => $hasError]) }}>
    @if ($placeholder || $includeBlank)
        <option value="">{{ $placeholder ?? '— Pilih —' }}</option>
    @endif
    @foreach ($options as $optValue => $optLabel)
        <option value="{{ $optValue }}" @selected((string) $optValue === (string) $selected)>{{ $optLabel }}</option>
    @endforeach
    {{ $slot }}
</select>
