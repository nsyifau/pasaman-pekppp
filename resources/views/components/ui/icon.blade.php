@props(['name', 'class' => 'h-4 w-4'])
<svg {{ $attributes->merge(['class' => $class]) }} aria-hidden="true" focusable="false">
    <use href="{{ asset('icons.svg') }}#i-{{ $name }}"></use>
</svg>

