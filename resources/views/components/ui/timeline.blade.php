@props(['items' => []])

<ol {{ $attributes->class('relative space-y-0') }}>
    @foreach($items as $item)
        <li class="relative border-l border-border pb-6 pl-7 last:border-transparent last:pb-0">
            <span @class(['absolute -left-[9px] top-0.5 flex h-4 w-4 items-center justify-center rounded-full border-2 border-card',
                'bg-positive' => ($item['tone'] ?? '') === 'success',
                'bg-destructive' => ($item['tone'] ?? '') === 'danger',
                'bg-primary' => ! in_array($item['tone'] ?? '', ['success', 'danger'], true)]) aria-hidden="true"></span>
            <div class="flex flex-wrap items-baseline justify-between gap-x-3 gap-y-1">
                <h3 class="text-sm font-semibold">{{ $item['title'] }}</h3>
                @if(!empty($item['time']))<time @if(!empty($item['datetime'])) datetime="{{ $item['datetime'] }}" @endif class="text-xs text-muted-foreground">{{ $item['time'] }}</time>@endif
            </div>
            @if(!empty($item['description']))<p class="mt-1 text-sm text-muted-foreground">{{ $item['description'] }}</p>@endif
        </li>
    @endforeach
</ol>
