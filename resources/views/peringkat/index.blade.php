@php
    $tone = fn (?string $t) => $t === 'secondary' ? 'neutral' : $t;
    $statuses = \App\Models\Evaluation::statuses();
@endphp

<x-layouts.admin title="Peringkat IPP" group="Laporan & Hasil">
    <x-ui.page-header title="Papan Peringkat Indeks Pelayanan Publik"
        description="Rekapitulasi peringkat unit layanan hasil penilaian F-01, F-02, dan F-03." />

    {{-- Statistik --}}
    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="card p-6">
            <h3 class="text-sm font-medium text-muted-foreground">Total Unit Terevaluasi</h3>
            <p class="mt-2 text-2xl font-semibold tracking-tight tabular-nums">{{ $categoryStats['total_units'] }} <span class="text-base font-normal text-muted-foreground">unit</span></p>
            <p class="mt-3 text-xs text-muted-foreground">Tahun Anggaran {{ $selectedYear }}</p>
        </div>
        <div class="card p-6">
            <h3 class="text-sm font-medium text-muted-foreground">Rata-Rata Indeks IPP</h3>
            <p class="mt-2 text-2xl font-semibold tracking-tight tabular-nums text-primary">{{ number_format($categoryStats['avg_ipp'], 3) }} <span class="text-base font-normal text-muted-foreground">/ 5,000</span></p>
            <p class="mt-3 text-xs text-muted-foreground">Skala pelayanan Pemkab Pasaman</p>
        </div>
        <div class="card p-6">
            <h3 class="text-sm font-medium text-muted-foreground">Peringkat 1 Tertinggi</h3>
            <p class="mt-2 truncate font-semibold" title="{{ $categoryStats['top_unit']?->opd?->name ?? '—' }}">{{ $categoryStats['top_unit']?->opd?->name ?? '—' }}</p>
            <p class="mt-3 text-xs text-muted-foreground">Skor {{ number_format((float) ($categoryStats['top_unit']?->final_score_ipp ?? 0), 3) }} ({{ $categoryStats['top_unit']?->category_ipp ?? '—' }})</p>
        </div>
        <div class="card p-6">
            <h3 class="text-sm font-medium text-muted-foreground">Kategori Prima &amp; Sangat Baik</h3>
            <p class="mt-2 text-2xl font-semibold tracking-tight tabular-nums text-info">{{ $categoryStats['prima_count'] + $categoryStats['sangat_baik_count'] }} <span class="text-base font-normal text-muted-foreground">unit (A / A-)</span></p>
            <p class="mt-3 text-xs text-muted-foreground">Kategori B: {{ $categoryStats['baik_count'] }} unit</p>
        </div>
    </section>

    {{-- Filter --}}
    <x-ui.filter-bar action="{{ route('peringkat.index') }}" searchName="q" :search="request('q')" placeholder="Cari nama unit layanan..."
        :resetUrl="(request()->filled('q') || $activeCategory !== 'all') ? route('peringkat.index') : null">
        <input type="hidden" name="kategori" id="kategoriInput" value="{{ $activeCategory }}">
        <div class="min-w-[16rem] flex-1 space-y-1.5">
            <span class="label">Filter Kategori Unit</span>
            <div class="flex flex-wrap gap-1" role="group" aria-label="Filter kategori">
                <button type="button" data-kategori="all" class="kategori-btn btn btn-sm {{ $activeCategory === 'all' ? 'btn-primary' : 'btn-outline' }}">Semua</button>
                @foreach ($categories as $key => $label)
                    <button type="button" data-kategori="{{ $key }}" class="kategori-btn btn btn-sm {{ $activeCategory === $key ? 'btn-primary' : 'btn-outline' }}">
                        {{ $key === 'opd' ? 'OPD' : ucfirst($key) }}
                    </button>
                @endforeach
            </div>
        </div>
        <x-ui.field label="Tahun Penilaian" name="tahun_filter" class="w-40">
            <x-ui.select name="tahun" placeholder="Semua tahun">
                @foreach ($availableYears as $y)
                    <option value="{{ $y }}" @selected($selectedYear === $y)>Tahun {{ $y }}</option>
                @endforeach
            </x-ui.select>
        </x-ui.field>
        <x-slot:active>
            @if (request()->filled('q'))<x-ui.badge variant="soft">Cari: {{ request('q') }}</x-ui.badge>@endif
            <x-ui.badge variant="{{ $activeCategory === 'all' ? 'soft' : 'info' }}">
                Kategori: {{ $activeCategory === 'all' ? 'Semua' : ($categories[$activeCategory] ?? $activeCategory) }}
            </x-ui.badge>
            <x-ui.badge variant="neutral">Tahun: {{ $selectedYear }}</x-ui.badge>
            <x-ui.badge variant="outline">{{ $rankings->count() }} unit</x-ui.badge>
        </x-slot:active>
    </x-ui.filter-bar>

    {{-- Tabel peringkat --}}
    <div class="card">
        <div class="flex flex-wrap items-center justify-between gap-4 p-6 pb-4">
            <div>
                <h2 class="card-title">Peringkat Unit Layanan</h2>
                <p class="card-desc mt-1.5">Diurutkan berdasarkan Indeks Pelayanan Publik tertinggi.</p>
            </div>
        </div>
        <div class="overflow-x-auto border-t">
            <table class="table min-w-[820px]">
                <thead>
                    <tr>
                        <th class="text-center">Peringkat</th>
                        <th>Unit Layanan / Lokus</th>
                        <th class="hidden md:table-cell">Kategori</th>
                        <th class="hidden text-right lg:table-cell">F-02</th>
                        <th class="hidden text-right lg:table-cell">F-03</th>
                        <th class="text-right">Indeks IPP</th>
                        <th class="text-center">Predikat</th>
                        <th class="hidden text-center md:table-cell">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rankings as $index => $row)
                        <tr @class(['bg-warning-soft/40' => $index === 0])>
                            <td class="text-center">
                                <span class="rank-badge {{ $index < 3 ? 'rank-'.($index + 1) : 'rank-other' }}">{{ $index + 1 }}</span>
                            </td>
                            <td class="font-medium">{{ $row->opd?->name ?? '—' }}</td>
                            <td class="hidden md:table-cell"><x-ui.badge variant="outline" class="badge-kategori">{{ $row->opd?->category ?? '—' }}</x-ui.badge></td>
                            <td class="hidden text-right tabular-nums lg:table-cell">{{ number_format((float) $row->score_f02, 3) }}</td>
                            <td class="hidden text-right tabular-nums lg:table-cell">{{ number_format((float) $row->score_f03, 3) }}</td>
                            <td class="text-right font-bold tabular-nums text-success">{{ number_format((float) $row->final_score_ipp, 3) }}</td>
                            <td class="text-center">
                                @if ($row->category_ipp)
                                    <x-ui.badge :variant="$tone($row->categoryTone())" class="badge-kategori">{{ $row->category_ipp }}</x-ui.badge>
                                    <span class="mt-1 block text-xs text-muted-foreground">{{ $row->category_ipp_label }}</span>
                                @else
                                    <span class="text-xs text-muted-foreground">—</span>
                                @endif
                            </td>
                            <td class="hidden text-center md:table-cell">
                                <x-ui.badge :variant="$tone(\App\Models\Evaluation::statusTone($row->status))" class="badge-kategori">{{ $statuses[$row->status] ?? $row->status }}</x-ui.badge>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <x-ui.empty icon="star" title="Belum ada data"
                                    description="Belum ada data evaluasi untuk kategori ini pada tahun {{ $selectedYear }}." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Referensi kategori --}}
    <x-ui.card title="Tabel Kategori Indeks Pelayanan Publik" description="Sesuai Keputusan Bupati Pasaman.">
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach (\App\Models\Evaluation::categoryRanges() as $code => $meta)
                <div class="flex items-center gap-3 rounded-lg border bg-muted/40 p-3">
                    <x-ui.badge :variant="$tone($meta['tone'])" class="px-3 py-2 text-base font-bold">{{ $code }}</x-ui.badge>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold">{{ $meta['range'] }}</p>
                        <p class="truncate text-xs text-muted-foreground" title="{{ $meta['label'] }}">{{ $meta['label'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </x-ui.card>
</x-layouts.admin>

@push('scripts')
    <script>
        document.querySelectorAll('.kategori-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                document.getElementById('kategoriInput').value = this.dataset.kategori;
                document.getElementById('kategoriInput').closest('form').submit();
            });
        });
    </script>
@endpush
