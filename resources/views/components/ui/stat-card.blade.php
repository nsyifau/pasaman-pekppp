@props([
    'title', 'value', 'icon', 'tile' => 'bg-soft text-soft-foreground', 'tone' => 'success',
    'direction' => 'up', 'delta', 'note' => 'dari bulan lalu', 'spark' => null,
])
<div {{ $attributes->class('card p-6') }}>
    <div class="flex items-start justify-between gap-3">
        <div>
            <h3 class="text-sm font-medium text-muted-foreground">{{ $title }}</h3>
            <p class="mt-2 text-2xl font-semibold tracking-tight tabular-nums">{{ $value }}</p>
        </div>
        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg {{ $tile }}">
            <x-ui.icon :name="$icon" class="h-5 w-5" />
        </span>
    </div>
    <p class="mt-3 flex items-center gap-2 text-xs">
        <span class="badge badge-{{ $tone }}"><x-ui.icon :name="'trend-'.$direction" class="h-3.5 w-3.5" />{{ $delta }}</span>
        <span class="text-muted-foreground">{{ $note }}</span>
    </p>
    @if ($spark)
        <div class="mt-4" data-chart="spark" data-kind="area" data-color="{{ $tone }}" data-values="{{ is_array($spark) ? Illuminate\Support\Js::from($spark) : $spark }}" data-height="36"></div>
    @endif
</div>

