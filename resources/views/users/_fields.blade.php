@php
    $user ??= null;
    $roles = $roles ?? \App\Models\User::roles();
    $opds = $opds ?? collect();
    $isEdit = $user !== null;
    $useOld = $useOld ?? false;
    $val = fn (string $field, $default = null) => $useOld ? old($field, $default) : $default;
@endphp

<div class="grid gap-4 sm:grid-cols-2">
    <x-ui.field label="Nama Lengkap" name="{{ $prefix }}_name" required>
        <x-ui.input id="{{ $prefix }}_name" name="name" :value="$val('name', $user?->name)" required maxlength="255" />
    </x-ui.field>

    <x-ui.field label="Username Login" name="{{ $prefix }}_username" required help="Dipakai untuk masuk (bukan email).">
        <x-ui.input id="{{ $prefix }}_username" name="username" :value="$val('username', $user?->username)" required maxlength="50" autocomplete="off" />
    </x-ui.field>

    <x-ui.field label="Email" name="{{ $prefix }}_email" required>
        <x-ui.input id="{{ $prefix }}_email" name="email" type="email" :value="$val('email', $user?->email)" required maxlength="255" />
    </x-ui.field>

    <x-ui.field label="Password" name="{{ $prefix }}_password" :required="! $isEdit"
        :help="$isEdit ? 'Kosongkan bila tidak ingin mengubah password.' : 'Minimal 8 karakter.'">
        <x-ui.input id="{{ $prefix }}_password" name="password" type="password" autocomplete="new-password" minlength="8" :required="! $isEdit" />
    </x-ui.field>

    <x-ui.field label="Peran / Hak Akses" name="{{ $prefix }}_role" required>
        <x-ui.select id="{{ $prefix }}_role" name="role" required data-role-select="{{ $prefix }}">
            <option value="">-- Pilih Peran --</option>
            @foreach ($roles as $key => $label)
                <option value="{{ $key }}" @selected($val('role', $user?->role ?? 'opd') === $key)>{{ $label }}</option>
            @endforeach
        </x-ui.select>
    </x-ui.field>

    <div data-opd-wrapper="{{ $prefix }}">
        <x-ui.field label="Unit Layanan Terkait" name="{{ $prefix }}_opd_id" help="Wajib diisi untuk peran Unit Layanan.">
            <x-ui.select id="{{ $prefix }}_opd_id" name="opd_id" searchable placeholder="Ketik untuk mencari unit...">
                <option value="">-- Pilih Unit Layanan --</option>
                @foreach ($opds as $opd)
                    <option value="{{ $opd->id }}" @selected((string) $val('opd_id', $user?->opd_id) === (string) $opd->id)>
                        [{{ $opd->category === 'opd' ? 'OPD' : ucfirst($opd->category) }}] {{ $opd->name }}
                    </option>
                @endforeach
            </x-ui.select>
        </x-ui.field>
    </div>

    <x-ui.field label="NIP Pengguna" name="{{ $prefix }}_nip">
        <x-ui.input id="{{ $prefix }}_nip" name="nip" :value="$val('nip', $user?->nip)" maxlength="50" />
    </x-ui.field>

    <x-ui.field label="Nomor WhatsApp / HP" name="{{ $prefix }}_phone">
        <x-ui.input id="{{ $prefix }}_phone" name="phone" :value="$val('phone', $user?->phone)" maxlength="50" />
    </x-ui.field>
</div>
