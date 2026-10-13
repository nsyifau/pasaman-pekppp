@php
    $opd ??= null;
    $categories = $categories ?? \App\Models\Opd::categories();
    $useOld = $useOld ?? false;
    $val = fn (string $field, $default = null) => $useOld ? old($field, $default) : $default;
@endphp

<div class="space-y-4">
    <x-ui.field label="Nama Unit Layanan" name="{{ $prefix }}_name" required>
        <x-ui.input id="{{ $prefix }}_name" name="name" :value="$val('name', $opd?->name)" required maxlength="255"
            placeholder="Contoh: Dinas Kependudukan dan Pencatatan Sipil" />
    </x-ui.field>

    <x-ui.field label="Kategori Unit" name="{{ $prefix }}_category" required>
        <x-ui.select id="{{ $prefix }}_category" name="category" required>
            <option value="">-- Pilih Kategori --</option>
            @foreach ($categories as $key => $label)
                <option value="{{ $key }}" @selected($val('category', $opd?->category) === $key)>{{ $label }}</option>
            @endforeach
        </x-ui.select>
    </x-ui.field>

    <x-ui.switch id="{{ $prefix }}_is_active" name="is_active" value="1"
        :checked="(bool) ($useOld ? old('is_active', true) : ($opd?->is_active ?? true))"
        label="Status Aktif"
        help="Unit nonaktif tidak tampil sebagai lokus penilaian." />
</div>
