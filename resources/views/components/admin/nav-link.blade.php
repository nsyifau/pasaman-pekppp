@props(['item'])
@php
    $active = request()->routeIs($item['active'] ?? $item['route']);
@endphp
<a href="{{ route($item['route']) }}" @class(['nav-link', 'active' => $active]) @if($active) aria-current="page" @endif>
    @if (!empty($item['icon']))
        <x-ui.icon :name="$item['icon']" class="h-4 w-4 shrink-0" />
    @endif
    <span class="sb-label flex-1 truncate">{{ $item['label'] }}</span>
    @if (!empty($item['badge']))
        <span class="sb-label rounded-full bg-soft px-1.5 py-0.5 text-xs font-medium tabular-nums text-soft-foreground">{{ $item['badge'] }}</span>
    @endif
</a>

