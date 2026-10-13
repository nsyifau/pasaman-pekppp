@props(['name' => null, 'value' => null, 'type' => 'text', 'invalid' => null, 'addon' => null, 'icon' => null])

@php
    $id = $attributes->get('id', $name ? 'f-' . str_replace(['[', ']'], '-', $name) : null);
    $hasError = $invalid ?? ($name && $errors->has($name));
    $inputValue = old($name, $value);
@endphp

@if ($addon || $icon)
    <div class="input-group relative">
        @if ($icon)
            <span class="input-addon absolute left-0 top-0 h-full border-0 bg-transparent px-2.5">
                <x-icon :name="$icon" class="h-4 w-4" />
            </span>
        @endif
        @if ($addon)
            <span class="input-addon">{{ $addon }}</span>
        @endif
        <input type="{{ $type }}" name="{{ $name }}" @if ($id) id="{{ $id }}" @endif
            @if (! is_null($inputValue) && $type !== 'file') value="{{ $inputValue }}" @endif
            @if ($hasError) aria-invalid="true" @endif
            {{ $attributes->class(['input', 'pl-8' => (bool) $icon, 'input-invalid' => $hasError]) }} />
    </div>
@else
    <input type="{{ $type }}" name="{{ $name }}" @if ($id) id="{{ $id }}" @endif
        @if (! is_null($inputValue) && $type !== 'file') value="{{ $inputValue }}" @endif
        @if ($hasError) aria-invalid="true" @endif
        {{ $attributes->class(['input', 'input-invalid' => $hasError]) }} />
@endif
