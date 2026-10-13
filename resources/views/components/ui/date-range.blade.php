@props(['id', 'label' => 'Rentang tanggal', 'fromName' => null, 'toName' => null, 'from' => null, 'to' => null])

<fieldset data-date-range {{ $attributes->class('space-y-3') }}>
    <legend class="label">{{ $label }}</legend>
    <div class="flex flex-wrap gap-2">
        <button type="button" class="btn btn-outline btn-sm" data-range-days="7">7 hari</button>
        <button type="button" class="btn btn-outline btn-sm" data-range-days="30">30 hari</button>
        <button type="button" class="btn btn-ghost btn-sm" data-range-clear>Hapus</button>
    </div>
    <div class="grid gap-3 sm:grid-cols-2">
        <div class="space-y-2"><label class="label" for="{{ $id }}-from">Dari</label><input id="{{ $id }}-from" @if($fromName) name="{{ $fromName }}" @endif type="text" class="input" value="{{ $fromName ? old($fromName, $from) : $from }}" placeholder="Pilih tanggal awal" autocomplete="off" data-date-picker data-range-from></div>
        <div class="space-y-2"><label class="label" for="{{ $id }}-to">Sampai</label><input id="{{ $id }}-to" @if($toName) name="{{ $toName }}" @endif type="text" class="input" value="{{ $toName ? old($toName, $to) : $to }}" placeholder="Pilih tanggal akhir" autocomplete="off" data-date-picker data-range-to></div>
    </div>
    <p class="field-help" role="status" aria-live="polite" data-range-status>Pilih tanggal atau gunakan preset.</p>
</fieldset>
