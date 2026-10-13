@props(['name', 'label' => null, 'checked' => false, 'value' => 1, 'hint' => null])

@php
    $id = $attributes->get('id', 'f-' . str_replace(['[', ']'], '-', $name));
    $isOn = (bool) old($name, $checked);
@endphp

<div class="flex items-start gap-3">
    <input type="hidden" name="{{ $name }}" value="0">
    <label class="switch">
        <input type="checkbox" id="{{ $id }}" name="{{ $name }}" value="{{ $value }}"
            @checked($isOn) {{ $attributes->class(['peer', 'sr-only']) }} />
        <span class="switch-track"></span>
    </label>
    @if ($label || $hint)
        <label for="{{ $id }}" class="cursor-pointer text-sm leading-tight">
            @if ($label)
                <span class="block font-medium">{{ $label }}</span>
            @endif
            @if ($hint)
                <span class="field-help mt-0.5 block">{{ $hint }}</span>
            @endif
        </label>
    @endif
</div>
