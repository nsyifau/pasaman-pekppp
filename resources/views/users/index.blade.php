<x-layouts.admin title="Pengguna" group="Master Data">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <x-ui.page-header title="Master Pengguna"
            description="Kelola akun pengguna, peran akses, dan penugasan unit layanan." />
        <button type="button" class="btn btn-primary" data-modal-open="#createUserModal">
            <x-ui.icon name="userplus" class="h-4 w-4" /> Tambah Pengguna
        </button>
    </div>

    <x-ui.filter-bar action="{{ route('users.index') }}" searchName="search" :search="$search"
        placeholder="Cari nama atau username"
        :resetUrl="($search !== '' || $role !== '' || $opd !== '') ? route('users.index') : null">
        <x-ui.field label="Peran" name="role" class="min-w-[12rem] flex-1">
            <x-ui.select name="role" placeholder="Semua peran">
                <option value="">Semua Peran</option>
                @foreach ($roles as $key => $label)
                    <option value="{{ $key }}" @selected($role === $key)>{{ $label }}</option>
                @endforeach
            </x-ui.select>
        </x-ui.field>
        <x-ui.field label="Unit Layanan" name="opd" class="min-w-[12rem] flex-1">
            <x-ui.select name="opd" searchable placeholder="Semua Unit">
                <option value="">Semua Unit</option>
                @foreach ($opds as $unit)
                    <option value="{{ $unit->id }}" @selected($opd === (string) $unit->id)>{{ $unit->name }}</option>
                @endforeach
            </x-ui.select>
        </x-ui.field>
        <x-slot:active>
            @if ($search !== '')<x-ui.badge variant="soft">Cari: {{ $search }}</x-ui.badge>@endif
            @if ($role !== '')<x-ui.badge variant="info">Peran: {{ $roles[$role] ?? $role }}</x-ui.badge>@endif
            @if ($opd !== '')<x-ui.badge variant="neutral">Unit: {{ $opds->firstWhere('id', (int) $opd)?->name ?? $opd }}</x-ui.badge>@endif
            <x-ui.badge variant="outline">{{ $users->total() }} pengguna</x-ui.badge>
        </x-slot:active>
    </x-ui.filter-bar>

    <div class="card">
        <div class="flex items-center justify-between gap-4 p-6 pb-4">
            <div>
                <h2 class="card-title">Daftar Pengguna</h2>
                <p class="card-desc mt-1.5">{{ $users->total() }} pengguna terdaftar.</p>
            </div>
        </div>
        <div class="overflow-x-auto border-t">
            <table class="table min-w-[560px]">
                <thead>
                    <tr>
                        <th>Nama / Username</th>
                        <th>Peran</th>
                        <th>Unit Lokus</th>
                        <th class="w-12 text-right" scope="col" aria-label="Aksi"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        @php
                            $roleTone = match ($user->role) {
                                'admin' => 'danger',
                                default => 'success',
                            };
                        @endphp
                        <tr>
                            <td>
                                <span class="block font-medium">{{ $user->name }}</span>
                                <span class="text-xs text-muted-foreground">{{ $user->username }}</span>
                            </td>
                            <td><x-ui.badge :variant="$roleTone" class="badge-kategori">{{ $roles[$user->role] ?? $user->role }}</x-ui.badge></td>
                            <td>
                                @if ($user->opd?->name)
                                    <x-ui.badge variant="outline">{{ $user->opd->name }}</x-ui.badge>
                                @else
                                    <span class="text-muted-foreground">—</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <div data-dropdown>
                                    <button type="button" data-dropdown-toggle aria-expanded="false" aria-label="Aksi untuk {{ $user->name }}" class="btn btn-ghost btn-icon btn-sm text-muted-foreground"><x-ui.icon name="more" /></button>
                                    <div class="menu menu-right" hidden>
                                        <button type="button" class="menu-item" data-modal-open="#editUserModal{{ $user->id }}"><x-ui.icon name="edit" />Ubah</button>
                                        <button type="button" class="menu-item" data-modal-open="#resetPasswordModal{{ $user->id }}"><x-ui.icon name="key" />Reset Password</button>
                                        @if ($user->id !== auth()->id())
                                            <div class="menu-sep"></div>
                                            <button type="button" class="menu-item menu-item-danger" data-modal-open="#delUserModal{{ $user->id }}"><x-ui.icon name="trash" />Hapus</button>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <x-ui.empty icon="users" title="Tidak ada pengguna"
                                    description="Belum ada data pengguna yang cocok dengan filter." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <x-ui.pagination :paginator="$users" />
        @endif
    </div>

    {{-- ===== Modal Tambah ===== --}}
    <x-ui.modal id="createUserModal" title="Tambah Pengguna" icon="userplus" tone="success">
        <form method="POST" action="{{ route('users.store') }}" autocomplete="off">
            @csrf
            @include('users._fields', ['prefix' => 'create', 'user' => null, 'useOld' => true, 'roles' => $roles, 'opds' => $opds])
            <div class="mt-6 flex flex-wrap justify-end gap-2 border-t pt-4">
                <button type="button" class="btn btn-outline" data-modal-close>Batal</button>
                <x-ui.button type="submit" variant="success">Simpan Data</x-ui.button>
            </div>
        </form>
    </x-ui.modal>

    {{-- ===== Modal Ubah, Reset Password, Hapus ===== --}}
    @foreach ($users as $user)
        <x-ui.modal id="editUserModal{{ $user->id }}" title="Ubah Data Pengguna" icon="edit">
            <form method="POST" action="{{ route('users.update', $user) }}" autocomplete="off">
                @csrf
                @method('PUT')
                <x-ui.alert variant="info" class="mb-4">
                    Sedang mengubah: <strong>{{ $user->name }}</strong> ({{ $user->username }})
                </x-ui.alert>
                @include('users._fields', ['prefix' => 'edit'.$user->id, 'user' => $user, 'useOld' => false, 'roles' => $roles, 'opds' => $opds])
                <div class="mt-6 flex flex-wrap justify-end gap-2 border-t pt-4">
                    <button type="button" class="btn btn-outline" data-modal-close>Batal</button>
                    <x-ui.button type="submit">Simpan Perubahan</x-ui.button>
                </div>
            </form>
        </x-ui.modal>

        <x-ui.modal id="resetPasswordModal{{ $user->id }}" title="Reset Password Pengguna" icon="key" tone="warning">
            <form method="POST" action="{{ route('users.reset_password', $user) }}" autocomplete="off">
                @csrf
                @method('PUT')
                <x-ui.alert variant="warning" class="mb-4">
                    Pengguna: <strong>{{ $user->name }}</strong> ({{ $user->username }})
                </x-ui.alert>
                <div class="space-y-4">
                    <x-ui.field label="Password Baru" name="password" required help="Minimal 8 karakter.">
                        <x-ui.input name="password" type="password" required minlength="8" autocomplete="new-password" />
                    </x-ui.field>
                    <x-ui.field label="Konfirmasi Password Baru" name="password_confirmation" required>
                        <x-ui.input name="password_confirmation" type="password" required minlength="8" autocomplete="new-password" />
                    </x-ui.field>
                </div>
                <div class="mt-6 flex flex-wrap justify-end gap-2 border-t pt-4">
                    <button type="button" class="btn btn-outline" data-modal-close>Batal</button>
                    <x-ui.button type="submit" variant="warning">Reset Password</x-ui.button>
                </div>
            </form>
        </x-ui.modal>

        @if ($user->id !== auth()->id())
            <x-ui.confirm-action
                id="delUserModal{{ $user->id }}"
                title="Hapus pengguna?"
                description="Akun &quot;{{ $user->name }}&quot; akan dihapus permanen."
                :action="route('users.destroy', $user)"
                method="DELETE"
                label="Ya, hapus" />
        @endif
    @endforeach
</x-layouts.admin>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[data-role-select]').forEach(function (select) {
                var prefix = select.dataset.roleSelect;
                var wrapper = document.querySelector('[data-opd-wrapper="' + prefix + '"]');
                var opdSelect = document.getElementById(prefix + '_opd_id');
                var sync = function () {
                    var isOpd = select.value === 'opd';
                    if (wrapper) wrapper.style.display = isOpd ? '' : 'none';
                    if (opdSelect) {
                        opdSelect.required = isOpd;
                        if (!isOpd) opdSelect.value = '';
                    }
                };
                select.addEventListener('change', sync);
                sync();
            });
        });
    </script>
@endpush
