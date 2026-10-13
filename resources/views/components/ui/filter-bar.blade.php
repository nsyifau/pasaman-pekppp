@props(['action', 'searchName' => 'q', 'search' => null, 'placeholder' => 'Cari data…', 'resetUrl' => null])

<div {{ $attributes->class('card p-4 sm:p-5') }}>
    <form method="GET" action="{{ $action }}" class="flex flex-wrap items-end gap-3">
        <div class="min-w-[12rem] flex-1 space-y-1.5">
            <label class="label" for="{{ $searchName }}-filter">Pencarian</label>
            <x-ui.input :id="$searchName.'-filter'" type="search" :name="$searchName" :value="$search ?? request($searchName)" :placeholder="$placeholder" icon="search" />
        </div>
        {{ $slot }}
        <x-ui.button type="submit" size="sm">Terapkan</x-ui.button>
        @if($resetUrl)
            <a href="{{ $resetUrl }}" class="btn btn-outline btn-sm">Atur ulang</a>
        @endif
    </form>
    @isset($active)
        <div class="mt-4 flex flex-wrap items-center gap-2 border-t pt-4 text-sm" aria-label="Filter aktif">
            <span class="text-muted-foreground">Filter aktif:</span>{{ $active }}
        </div>
    @endisset
</div>
