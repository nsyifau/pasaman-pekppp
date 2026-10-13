@props(['title' => null, 'items' => [], 'columns' => 2])

<section {{ $attributes->class('card p-5 sm:p-6') }}>
    @if($title)<h2 class="mb-5 text-base font-semibold">{{ $title }}</h2>@endif
    <dl @class(['grid gap-x-8 gap-y-5', 'sm:grid-cols-2' => $columns === 2 || $columns === '2'])>
        @foreach($items as $item)
            <div class="min-w-0">
                <dt class="text-xs font-medium text-muted-foreground">{{ $item['label'] }}</dt>
                <dd class="mt-1 break-words text-sm font-medium">{{ $item['value'] ?? '—' }}</dd>
            </div>
        @endforeach
        {{ $slot }}
    </dl>
</section>
