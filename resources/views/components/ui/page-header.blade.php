@props(['title', 'description' => null])
<div {{ $attributes->class('flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between') }}>
    <div class="min-w-0">
        <h1 class="text-2xl font-semibold tracking-tight">{{ $title }}</h1>
        @if ($description)
            <p class="text-muted-foreground">{{ $description }}</p>
        @endif
    </div>
    @if (trim($slot))
        <div class="flex shrink-0 flex-wrap items-center gap-2">{{ $slot }}</div>
    @endif
</div>
