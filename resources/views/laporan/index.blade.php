@php
    $statuses = \App\Models\Evaluation::statuses();
    $tone = fn (?string $t) => $t === 'secondary' ? 'neutral' : $t;
@endphp

<x-layouts.admin title="Laporan & Cetak" group="Laporan & Hasil">
    <x-ui.page-header title="Laporan & Cetak Dokumen"
        description="Cetak Berita Acara dan Rekapitulasi hasil penilaian per unit layanan." />

    <x-ui.filter-bar action="{{ route('laporan.index') }}" searchName="year" :search="$year"
        placeholder="Kosongkan untuk semua tahun..."
        :resetUrl="$year !== '' ? route('laporan.index') : null">
        <div class="w-40 space-y-1.5">
            <label class="label" for="tahun-filter">Tahun Penilaian</label>
            <x-ui.select name="year" id="tahun-filter">
                <option value="">Semua Tahun</option>
                @foreach ($availableYears as $y)
                    <option value="{{ $y }}" @selected((string) $y === (string) $year)>Tahun {{ $y }}</option>
                @endforeach
            </x-ui.select>
        </div>
    </x-ui.filter-bar>

    <div class="card">
        <div class="flex flex-wrap items-center justify-between gap-4 p-6 pb-4">
            <div>
                <h2 class="card-title">Daftar Unit Layanan</h2>
                <p class="card-desc mt-1.5">Pilih dokumen yang ingin dicetak untuk setiap unit.</p>
            </div>
            <x-ui.badge variant="outline">{{ $evaluations->count() }} unit</x-ui.badge>
        </div>

        <div class="overflow-x-auto border-t">
            <table class="table min-w-[860px]">
                <thead>
                    <tr>
                        <th>Unit Layanan / Lokus</th>
                        <th class="hidden md:table-cell">Kategori</th>
                        <th class="hidden lg:table-cell text-right">F-02</th>
                        <th class="hidden lg:table-cell text-right">F-03</th>
                        <th class="text-right">Indeks IPP</th>
                        <th class="text-center">Predikat</th>
                        <th class="hidden md:table-cell text-center">Status</th>
                        <th class="text-right">Dokumen</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($evaluations as $row)
                        <tr>
                            <td class="font-medium">{{ $row->opd?->name ?? '—' }}</td>
                            <td class="hidden md:table-cell">
                                <x-ui.badge variant="outline" class="badge-kategori">{{ $categories[$row->opd?->category] ?? $row->opd?->category ?? '—' }}</x-ui.badge>
                            </td>
                            <td class="hidden lg:table-cell text-right tabular-nums">{{ number_format((float) $row->score_f02, 3) }}</td>
                            <td class="hidden lg:table-cell text-right tabular-nums">{{ number_format((float) $row->score_f03, 3) }}</td>
                            <td class="text-right font-bold tabular-nums text-success">{{ number_format((float) $row->final_score_ipp, 3) }}</td>
                            <td class="text-center">
                                @if ($row->category_ipp)
                                    <x-ui.badge :variant="$tone($row->categoryTone())" class="badge-kategori">{{ $row->category_ipp }}</x-ui.badge>
                                @else
                                    <span class="text-xs text-muted-foreground">—</span>
                                @endif
                            </td>
                            <td class="hidden md:table-cell text-center">
                                <x-ui.badge :variant="$tone(\App\Models\Evaluation::statusTone($row->status))" class="badge-kategori">{{ $statuses[$row->status] ?? $row->status }}</x-ui.badge>
                            </td>
                            <td>
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('laporan.berita_acara', $row) }}" target="_blank" rel="noopener"
                                        class="btn btn-outline btn-sm"><x-ui.icon name="file-text" class="h-4 w-4" /> Berita Acara</a>
                                    <a href="{{ route('laporan.rekapitulasi', $row) }}" target="_blank" rel="noopener"
                                        class="btn btn-soft btn-sm"><x-ui.icon name="table" class="h-4 w-4" /> Rekapitulasi</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <x-ui.table-state state="empty" title="Belum ada sesi evaluasi"
                                    description="Buat sesi evaluasi unit pada menu Evaluasi Unit terlebih dahulu." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
