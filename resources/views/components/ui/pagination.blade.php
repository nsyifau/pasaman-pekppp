@props(['paginator'])

@if ($paginator->hasPages())
    <div class="flex flex-wrap items-center justify-between gap-3 border-t px-6 py-3">
        <p class="text-xs text-muted-foreground">
            Menampilkan {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }}
            dari {{ $paginator->total() }}
        </p>

        <nav class="flex items-center gap-1" role="navigation" aria-label="Paginasi">
            @if ($paginator->onFirstPage())
                <span class="page-btn pointer-events-none opacity-40" aria-hidden="true">
                    <x-icon name="chevron-left" class="h-4 w-4" />
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="page-btn" aria-label="Halaman sebelumnya">
                    <x-icon name="chevron-left" class="h-4 w-4" />
                </a>
            @endif

            @foreach ($paginator->linkCollection()->slice(1, -1) as $element)
                @if ($element['url'] === null)
                    <span class="page-btn pointer-events-none opacity-40">{{ $element['label'] }}</span>
                @elseif ($element['active'])
                    <span class="page-btn" aria-current="page">{{ $element['label'] }}</span>
                @else
                    <a href="{{ $element['url'] }}" class="page-btn">{{ $element['label'] }}</a>
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="page-btn" aria-label="Halaman berikutnya">
                    <x-icon name="chevron-right" class="h-4 w-4" />
                </a>
            @else
                <span class="page-btn pointer-events-none opacity-40" aria-hidden="true">
                    <x-icon name="chevron-right" class="h-4 w-4" />
                </span>
            @endif
        </nav>
    </div>
@endif
