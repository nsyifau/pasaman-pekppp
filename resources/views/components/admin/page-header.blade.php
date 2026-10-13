@props(['title', 'subtitle' => null, 'group' => null])

<div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div class="min-w-0">
        <h1 class="truncate text-xl font-semibold tracking-tight">{{ $title }}</h1>
        @if ($subtitle)
            <p class="mt-0.5 text-sm text-muted-foreground">{{ $subtitle }}</p>
        @endif
    </div>

    @if (trim($slot))
        <div class="flex shrink-0 flex-wrap items-center gap-2">{{ $slot }}</div>
    @endif
</div>
