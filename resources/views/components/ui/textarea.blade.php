@props(['name' => null, 'value' => null, 'rows' => 4, 'invalid' => null])

@php
    $id = $attributes->get('id', $name ? 'f-' . str_replace(['[', ']'], '-', $name) : null);
    $hasError = $invalid ?? ($name && $errors->has($name));
@endphp

<textarea name="{{ $name }}" rows="{{ $rows }}" @if ($id) id="{{ $id }}" @endif
    @if ($hasError) aria-invalid="true" @endif
    {{ $attributes->class(['textarea', 'input-invalid' => $hasError]) }}>{{ old($name, $value) }}</textarea>
