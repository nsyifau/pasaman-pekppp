@props(['id', 'label' => 'Pilih berkas', 'accept' => 'image/png,image/jpeg,application/pdf', 'maxMb' => 5])

<div data-file-preview data-max-mb="{{ $maxMb }}" {{ $attributes->class('space-y-3') }}>
    <label for="{{ $id }}" class="dropzone cursor-pointer focus-within:border-primary focus-within:ring-2 focus-within:ring-ring/50" data-file-drop>
        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-soft text-soft-foreground"><x-ui.icon name="upload" class="h-5 w-5" /></span>
        <span class="font-medium">{{ $label }}</span>
        <span class="field-help">Seret ke sini atau klik untuk memilih. PNG, JPG, PDF · maksimal {{ $maxMb }} MB.</span>
        <input id="{{ $id }}" type="file" class="sr-only" accept="{{ $accept }}" data-file-input>
    </label>
    <div class="flex items-center gap-3 rounded-lg border bg-card p-3" hidden data-file-result>
        <img class="h-12 w-12 rounded-md object-cover" alt="Pratinjau berkas terpilih" hidden data-file-image>
        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-md bg-soft text-soft-foreground" data-file-icon><x-ui.icon name="file-text" /></span>
        <div class="min-w-0 flex-1"><p class="truncate text-sm font-medium" data-file-name></p><p class="text-xs text-muted-foreground" data-file-size></p></div>
        <button type="button" class="btn btn-ghost btn-icon btn-sm" aria-label="Hapus berkas" data-file-remove><x-ui.icon name="x" /></button>
    </div>
    <p class="field-error" role="status" aria-live="polite" hidden data-file-error></p>
</div>
