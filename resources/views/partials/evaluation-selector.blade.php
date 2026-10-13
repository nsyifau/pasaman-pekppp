@php
    $selectorRoute = $selectorRoute ?? 'dashboard';
    $evaluations = $evaluations ?? collect();
    $selectedEvaluationId = $selectedEvaluationId ?? null;
    $statuses = \App\Models\Evaluation::statuses();
@endphp

<form method="GET" action="{{ route($selectorRoute) }}" data-auto-submit class="space-y-3">
    <div class="space-y-1.5">
        <label class="label" for="evaluasi-selector">
            <x-ui.icon name="layers" class="inline h-3.5 w-3.5 text-muted-foreground" /> Pilih Unit Layanan / Lokus Evaluasi
        </label>
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <x-ui.select id="evaluasi-selector" name="evaluasi" searchable placeholder="Ketik untuk mencari unit..." class="flex-1">
                @forelse ($evaluations as $item)
                    <option value="{{ $item->id }}" @selected($item->id === $selectedEvaluationId)>
                        [{{ strtoupper($item->opd?->category ?? 'OPD') }}] {{ $item->opd?->name ?? '-' }} (Tahun {{ $item->year }}) — {{ $statuses[$item->status] ?? $item->status }}
                    </option>
                @empty
                    <option value="">-- Belum ada data evaluasi --</option>
                @endforelse
            </x-ui.select>
            <x-ui.button type="submit" variant="soft" size="sm" class="shrink-0 sm:self-auto">
                <x-ui.icon name="refresh" class="h-4 w-4" /> Muat Data Unit
            </x-ui.button>
        </div>
        <p class="field-help">Pilih unit untuk dimuat ke formulir penilaian.</p>
    </div>
</form>
