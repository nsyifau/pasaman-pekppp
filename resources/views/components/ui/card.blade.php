@props(['title' => null, 'description' => null])
<section {{ $attributes->class('card') }}>
    @if ($title || $description || isset($header))
        <div class="card-header">
            @isset($header)
                {{ $header }}
            @else
                @if ($title)<h2 class="card-title">{{ $title }}</h2>@endif
                @if ($description)<p class="card-desc">{{ $description }}</p>@endif
            @endisset
        </div>
    @endif
    <div class="card-content">
        {{ $slot }}
    </div>
    @isset($footer)
        <div class="card-footer">{{ $footer }}</div>
    @endisset
</section>

