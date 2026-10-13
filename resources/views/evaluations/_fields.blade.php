@php
    $evaluation ??= null;
    $opds = $opds ?? collect();
    $statuses = $statuses ?? \App\Models\Evaluation::statuses();
    $useOld = $useOld ?? false;
    $val = fn (string $field, $default = null) => $useOld ? old($field, $default) : $default;
@endphp

<div class="grid gap-4 sm:grid-cols-3">
    <x-ui.field label="Unit Layanan / Lokus" name="{{ $prefix }}_opd_id" required class="sm:col-span-2">
        <x-ui.select id="{{ $prefix }}_opd_id" name="opd_id" required searchable placeholder="Ketik untuk mencari unit...">
            <option value="">-- Pilih Unit Layanan --</option>
            @foreach ($opds as $opd)
                <option value="{{ $opd->id }}" @selected((string) $val('opd_id', $evaluation?->opd_id) === (string) $opd->id)>
                    [{{ $opd->category === 'opd' ? 'OPD' : ucfirst($opd->category) }}] {{ $opd->name }}
                </option>
            @endforeach
        </x-ui.select>
    </x-ui.field>

    <x-ui.field label="Tahun Evaluasi" name="{{ $prefix }}_year" required>
        <x-ui.input id="{{ $prefix }}_year" name="year" type="number" :value="$val('year', $evaluation?->year ?? date('Y'))" required min="2000" max="2100" />
    </x-ui.field>

    <x-ui.field label="Status Evaluasi" name="{{ $prefix }}_status" required>
        <x-ui.select id="{{ $prefix }}_status" name="status" required>
            @foreach ($statuses as $key => $label)
                <option value="{{ $key }}" @selected($val('status', $evaluation?->status ?? 'draft') === $key)>{{ $label }}</option>
            @endforeach
        </x-ui.select>
    </x-ui.field>

    <x-ui.field label="Skor Saat Ini" class="sm:col-span-2" help="Skor dihitung otomatis dari instrumen F-02 dan F-03.">
        <div class="grid grid-cols-3 gap-2">
            <div class="rounded-md border bg-muted/40 px-3 py-2 text-center">
                <span class="block text-xs text-muted-foreground">F-02</span>
                <span class="font-semibold tabular-nums">{{ $evaluation ? number_format((float) $evaluation->score_f02, 3) : '—' }}</span>
            </div>
            <div class="rounded-md border bg-muted/40 px-3 py-2 text-center">
                <span class="block text-xs text-muted-foreground">F-03</span>
                <span class="font-semibold tabular-nums">{{ $evaluation ? number_format((float) $evaluation->score_f03, 3) : '—' }}</span>
            </div>
            <div class="rounded-md border bg-muted/40 px-3 py-2 text-center">
                <span class="block text-xs text-muted-foreground">IPP</span>
                <span class="font-semibold tabular-nums text-success">{{ $evaluation ? number_format((float) $evaluation->final_score_ipp, 3) : '—' }}</span>
            </div>
        </div>
    </x-ui.field>

    <x-ui.field label="Catatan Penilai" name="{{ $prefix }}_evaluator_notes" class="sm:col-span-3">
        <x-ui.textarea id="{{ $prefix }}_evaluator_notes" name="evaluator_notes" rows="3"
            placeholder="Catatan umum untuk seluruh sesi evaluasi unit ini...">{{ $val('evaluator_notes', $evaluation?->evaluator_notes) }}</x-ui.textarea>
    </x-ui.field>
</div>
