@props(['id', 'label', 'options' => [], 'name' => null, 'placeholder' => 'Cari dan pilih…'])

<div data-combobox {{ $attributes->class('relative min-w-0 space-y-2') }}>
    <label class="label" for="{{ $id }}">{{ $label }}</label>
    <input id="{{ $id }}" class="input" type="text" role="combobox" aria-autocomplete="list" aria-haspopup="listbox" aria-expanded="false" aria-controls="{{ $id }}-list" autocomplete="off" placeholder="{{ $placeholder }}" data-combobox-input>
    <input type="hidden" @if ($name) name="{{ $name }}" @endif data-combobox-value>
    <div id="{{ $id }}-list" role="listbox" aria-label="{{ $label }}" class="absolute z-30 mt-1 max-h-56 w-full overflow-y-auto rounded-lg border bg-card p-1 shadow-lg" hidden data-combobox-list>
        @foreach ($options as $option)
            @php
                $optionValue = is_array($option) ? $option['value'] : $option;
                $optionLabel = is_array($option) ? $option['label'] : $option;
            @endphp
            <div id="{{ $id }}-option-{{ $loop->index }}" role="option" aria-selected="false" class="cursor-pointer rounded-md px-3 py-2 text-sm hover:bg-accent" data-combobox-option data-value="{{ $optionValue }}">{{ $optionLabel }}</div>
        @endforeach
        <p class="px-3 py-2 text-sm text-muted-foreground" role="status" hidden data-combobox-empty>Tidak ada pilihan yang cocok.</p>
    </div>
</div>
