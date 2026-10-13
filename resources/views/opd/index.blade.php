<x-layouts.admin title="Unit Layanan" group="Master Data">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <x-ui.page-header title="Master Unit Layanan / OPD"
            description="Kelola data unit layanan yang menjadi lokus Penilaian Kinerja Pelayanan Publik." />
        <button type="button" class="btn btn-primary" data-modal-open="#createOpdModal">
            <x-ui.icon name="plus" class="h-4 w-4" /> Tambah Unit
        </button>
    </div>

    <x-ui.filter-bar action="{{ route('opds.index') }}" searchName="search" :search="$search"
        placeholder="Cari nama unit layanan..."
        :resetUrl="($search !== '' || $category !== '' || $status !== '') ? route('opds.index') : null">
        <x-ui.field label="Kategori" name="category" class="min-w-[12rem] flex-1">
            <x-ui.select name="category" placeholder="Semua kategori">
                <option value="">Semua Kategori</option>
                @foreach ($categories as $key => $label)
                    <option value="{{ $key }}" @selected($category === $key)>{{ $label }}</option>
                @endforeach
            </x-ui.select>
        </x-ui.field>
        <x-ui.field label="Status" name="status" class="min-w-[10rem] flex-1">
            <x-ui.select name="status" placeholder="Semua status">
                <option value="">Semua Status</option>
                <option value="active" @selected($status === 'active')>Aktif</option>
                <option value="inactive" @selected($status === 'inactive')>Nonaktif</option>
            </x-ui.select>
        </x-ui.field>
        <x-slot:active>
            @if ($search !== '')<x-ui.badge variant="soft">Cari: {{ $search }}</x-ui.badge>@endif
            @if ($category !== '')<x-ui.badge variant="info">Kategori: {{ $categories[$category] ?? $category }}</x-ui.badge>@endif
            @if ($status !== '')<x-ui.badge variant="neutral">Status: {{ $status === 'active' ? 'Aktif' : 'Nonaktif' }}</x-ui.badge>@endif
            <x-ui.badge variant="outline">{{ $opds->total() }} unit</x-ui.badge>
        </x-slot:active>
    </x-ui.filter-bar>

    <div class="card">
        <div class="flex items-center justify-between gap-4 p-6 pb-4">
            <div>
                <h2 class="card-title">Daftar Unit Layanan</h2>
                <p class="card-desc mt-1.5">{{ $opds->total() }} unit terdaftar.</p>
            </div>
        </div>
        <div class="overflow-x-auto border-t">
            <table class="table min-w-[760px]">
                <thead>
                    <tr>
                        <th>Unit Layanan</th>
                        <th>Kategori</th>
                        <th class="hidden text-right md:table-cell">Pengguna</th>
                        <th class="hidden text-right md:table-cell">Evaluasi</th>
                        <th>Status</th>
                        <th class="w-12 text-right" scope="col" aria-label="Aksi"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($opds as $opd)
                        <tr>
                            <td class="font-medium">{{ $opd->name }}</td>
                            <td><x-ui.badge variant="soft">{{ $categories[$opd->category] ?? $opd->category }}</x-ui.badge></td>
                            <td class="hidden text-right tabular-nums md:table-cell">{{ $opd->users_count }}</td>
                            <td class="hidden text-right tabular-nums md:table-cell">{{ $opd->evaluations_count }}</td>
                            <td>
                                @if ($opd->is_active)
                                    <x-ui.badge variant="success" dot>Aktif</x-ui.badge>
                                @else
                                    <x-ui.badge variant="neutral" dot>Nonaktif</x-ui.badge>
                                @endif
                            </td>
                            <td class="text-right">
                                <div data-dropdown>
                                    <button type="button" data-dropdown-toggle aria-expanded="false" aria-label="Aksi untuk {{ $opd->name }}" class="btn btn-ghost btn-icon btn-sm text-muted-foreground"><x-ui.icon name="more" /></button>
                                    <div class="menu menu-right" hidden>
                                        <button type="button" class="menu-item" data-modal-open="#editOpdModal{{ $opd->id }}"><x-ui.icon name="edit" />Ubah</button>
                                        <div class="menu-sep"></div>
                                        <button type="button" class="menu-item menu-item-danger" data-modal-open="#delOpdModal{{ $opd->id }}"><x-ui.icon name="trash" />Hapus</button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <x-ui.empty icon="layers" title="Tidak ada unit layanan"
                                    description="Belum ada data unit layanan yang cocok dengan filter." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($opds->hasPages())
            <x-ui.pagination :paginator="$opds" />
        @endif
    </div>

    {{-- ===== Modal Tambah ===== --}}
    <x-ui.modal id="createOpdModal" title="Tambah Unit Layanan" icon="plus" tone="success">
        <form method="POST" action="{{ route('opds.store') }}">
            @csrf
            @include('opd._fields', ['prefix' => 'create', 'opd' => null, 'useOld' => true, 'categories' => $categories])
            <div class="mt-6 flex flex-wrap justify-end gap-2 border-t pt-4">
                <button type="button" class="btn btn-outline" data-modal-close>Batal</button>
                <x-ui.button type="submit" variant="success">Simpan Data</x-ui.button>
            </div>
        </form>
    </x-ui.modal>

    {{-- ===== Modal Ubah & Konfirmasi Hapus ===== --}}
    @foreach ($opds as $opd)
        <x-ui.modal id="editOpdModal{{ $opd->id }}" title="Ubah Data Unit Layanan" icon="edit">
            <form method="POST" action="{{ route('opds.update', $opd) }}">
                @csrf
                @method('PUT')
                <x-ui.alert variant="info" class="mb-4">
                    Sedang mengubah: <strong>{{ $opd->name }}</strong>
                </x-ui.alert>
                @include('opd._fields', ['prefix' => 'edit'.$opd->id, 'opd' => $opd, 'useOld' => false, 'categories' => $categories])
                <div class="mt-6 flex flex-wrap justify-end gap-2 border-t pt-4">
                    <button type="button" class="btn btn-outline" data-modal-close>Batal</button>
                    <x-ui.button type="submit">Simpan Perubahan</x-ui.button>
                </div>
            </form>
        </x-ui.modal>

        <x-ui.confirm-action
            id="delOpdModal{{ $opd->id }}"
            title="Hapus unit layanan?"
            description="Unit layanan &quot;{{ $opd->name }}&quot; akan dihapus permanen."
            :action="route('opds.destroy', $opd)"
            method="DELETE"
            label="Ya, hapus" />
    @endforeach
</x-layouts.admin>
