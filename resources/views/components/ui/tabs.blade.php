@props(['id', 'tabs', 'variant' => 'underline', 'active' => null])

@php
    $style = in_array($variant, ['underline', 'pill', 'soft'], true) ? $variant : 'underline';
    $selected = $active ?? array_key_first($tabs);
@endphp
<div {{ $attributes->except('aria-label')->class('min-w-0') }} data-tabs>
    <div class="tabs-{{ $style }}" role="tablist" aria-label="{{ $attributes->get('aria-label', 'Navigasi tab') }}">
        @foreach($tabs as $key => $label)
            <button type="button" role="tab" class="tab" id="{{ $id }}-tab-{{ $key }}" aria-controls="{{ $id }}-panel-{{ $key }}"
                aria-selected="{{ $key == $selected ? 'true' : 'false' }}" tabindex="{{ $key == $selected ? '0' : '-1' }}">{{ $label }}</button>
        @endforeach
    </div>
    @foreach($tabs as $key => $label)
        <div role="tabpanel" id="{{ $id }}-panel-{{ $key }}" aria-labelledby="{{ $id }}-tab-{{ $key }}" class="pt-4" @if($key != $selected) hidden @endif>
            {{ ${'tab_'.$key} ?? '' }}
        </div>
    @endforeach
</div>
