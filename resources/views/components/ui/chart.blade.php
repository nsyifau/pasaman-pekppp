@props(['type', 'labels' => [], 'series' => [], 'items' => [], 'values' => [], 'height' => 240, 'aria' => 'Grafik', 'summary' => null, 'legend' => false, 'smooth' => false, 'prefix' => '', 'suffix' => '', 'size' => null, 'totalLabel' => null])

<figure {{ $attributes->class('min-w-0') }}>
    <div data-chart="{{ $type }}" data-labels="{{ json_encode($labels) }}" data-series="{{ json_encode($series) }}" data-items="{{ json_encode($items) }}" data-values="{{ json_encode($values) }}" data-height="{{ $height }}" data-aria="{{ $aria }}" data-prefix="{{ $prefix }}" data-suffix="{{ $suffix }}" @if ($legend) data-legend @endif @if ($smooth) data-smooth @endif @if ($size) data-size="{{ $size }}" @endif @if ($totalLabel) data-total-label="{{ $totalLabel }}" @endif></div>
    @if ($summary)
        <figcaption class="mt-3 text-sm text-muted-foreground">{{ $summary }}</figcaption>
    @endif
</figure>
