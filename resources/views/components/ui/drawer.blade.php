@props(['id', 'title', 'description' => null])

<dialog id="{{ $id }}" aria-labelledby="{{ $id }}-title" @if ($description) aria-describedby="{{ $id }}-description" @endif {{ $attributes->class('drawer') }}>
    <div class="flex h-full flex-col">
        <div class="flex items-start justify-between gap-4 border-b p-5">
            <div class="min-w-0">
                <h2 id="{{ $id }}-title" class="font-semibold">{{ $title }}</h2>
                @if ($description)
                    <p id="{{ $id }}-description" class="mt-1 text-sm text-muted-foreground">{{ $description }}</p>
                @endif
            </div>
            <button type="button" class="btn btn-ghost btn-icon btn-sm shrink-0" data-modal-close aria-label="Tutup {{ $title }}"><x-ui.icon name="x" /></button>
        </div>
        <div class="min-h-0 flex-1 overflow-y-auto p-5">{{ $slot }}</div>
        @isset($footer)
            <div class="flex gap-2 border-t p-5">{{ $footer }}</div>
        @endisset
    </div>
</dialog>
