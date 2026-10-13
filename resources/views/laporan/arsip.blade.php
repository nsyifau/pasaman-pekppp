@php
    $tone = fn (?string $t) => $t === 'secondary' ? 'neutral' : $t;
    $evalStatuses = \App\Models\Evaluation::statuses();
@endphp

<x-layouts.admin title="Arsip Tahun" group="Laporan & Hasil">
    <x-ui.page-header title="Arsip Penilaian Tahunan"
        description="Data periode yang sudah ditutup atau dikunci (read-only) beserta dokumen cetaknya." />

    @if ($periods->isEmpty())
        <x-ui.alert variant="info" title="Belum Ada Arsip">
            Arsip akan terisi setelah ada periode penilaian yang ditutup atau dikunci.
        </x-ui.alert>
    @else
        <x-ui.filter-bar action="{{ route('arsip.index') }}" searchName="tahun" :search="$selectedYear"
            placeholder="Tahun" :resetUrl="null">
            <div class="w-40 space-y-1.5">
                <label class="label" for="arsip-tahun">Tahun Arsip</label>
                <x-ui.select name="tahun" id="arsip-tahun">
                    @foreach ($periods as $p)
                        <option value="{{ $p->year }}" @selected($selectedYear === $p->year)>
                            {{ $p->year }} — {{ $statuses[$p->status] ?? $p->status }}
                        </option>
                    @endforeach
                </x-ui.select>
            </div>
        </x-ui.filter-bar>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="card p-6">
                <h3 class="text-sm font-medium text-muted-foreground">Tahun Arsip</h3>
                <p class="mt-2 text-2xl font-semibold tabular-nums">{{ $selectedYear }}</p>
                @if ($selectedPeriod)
                    <div class="mt-3">
                        <x-ui.badge :variant="$tone(\App\Models\EvaluationPeriod::statusTone($selectedPeriod->status))">
                            {{ $statuses[$selectedPeriod->status] ?? $selectedPeriod->status }}
                        </x-ui.badge>
                    </div>
                @endif
            </div>
            <div class="card p-6">
                <h3 class="text-sm font-medium text-muted-foreground">Unit Terevaluasi</h3>
                <p class="mt-2 text-2xl font-semibold tabular-nums">{{ $evaluations->count() }} <span class="text-base font-normal text-muted-foreground">unit</span></p>
            </div>
            <div class="card p-6">
                <h3 class="text-sm font-medium text-muted-foreground">Rata-Rata IPP</h3>
                <p class="mt-2 text-2xl font-semibold tabular-nums text-primary">{{ number_format((float) ($evaluations->avg('final_score_ipp') ?? 0), 3) }}</p>
            </div>
            <div class="card p-6">
                <h3 class="text-sm font-medium text-muted-foreground">Ditutup / Dikunci</h3>
                <p class="mt-2 text-sm font-medium">
                    @if ($selectedPeriod?->closed_at)
                        <span class="block">Ditutup: {{ $selectedPeriod->closed_at->format('d/m/Y H:i') }}</span>
                    @endif
                    @if ($selectedPeriod?->locked_at)
                        <span class="block text-danger">Dikunci: {{ $selectedPeriod->locked_at->format('d/m/Y H:i') }}</span>
                    @endif
                    @if (! $selectedPeriod?->closed_at && ! $selectedPeriod?->locked_at)
                        <span class="text-muted-foreground">—</span>
                    @endif
                </p>
            </div>
        </div>

        <div class="card">
            <div class="flex flex-wrap items-center justify-between gap-4 p-6 pb-4">
                <div>
                    <h2 class="card-title">Hasil Penilaian Tahun {{ $selectedYear }}</h2>
                    <p class="card-desc mt-1.5">Diurutkan berdasarkan Indeks Pelayanan Publik tertinggi.</p>
                </div>
                <x-ui.badge variant="outline">{{ $evaluations->count() }} unit</x-ui.badge>
            </div>

            <div class="overflow-x-auto border-t">
                <table class="table min-w-[900px]">
                    <thead>
                        <tr>
                            <th class="text-center">Peringkat</th>
                            <th>Unit Layanan / Lokus</th>
                            <th class="hidden lg:table-cell text-right">F-02</th>
                            <th class="hidden lg:table-cell text-right">F-03</th>
                            <th class="text-right">Indeks IPP</th>
                            <th class="text-center">Predikat</th>
                            <th class="hidden md:table-cell text-center">Status</th>
                            <th class="text-right">Dokumen</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($evaluations as $index => $row)
                            <tr>
                                <td class="text-center">
                                    <span class="rank-badge {{ $index < 3 ? 'rank-'.($index + 1) : 'rank-other' }}">{{ $index + 1 }}</span>
                                </td>
                                <td class="font-medium">{{ $row->opd?->name ?? '—' }}</td>
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
                                    <x-ui.badge :variant="$tone(\App\Models\Evaluation::statusTone($row->status))" class="badge-kategori">{{ $evalStatuses[$row->status] ?? $row->status }}</x-ui.badge>
                                </td>
                                <td>
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('laporan.berita_acara', $row) }}" target="_blank" rel="noopener" class="btn btn-outline btn-sm">
                                            <x-ui.icon name="file-text" class="h-4 w-4" /> Berita Acara
                                        </a>
                                        <a href="{{ route('laporan.rekapitulasi', $row) }}" target="_blank" rel="noopener" class="btn btn-soft btn-sm">
                                            <x-ui.icon name="table" class="h-4 w-4" /> Rekap
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <x-ui.empty icon="clock" title="Tidak ada data"
                                        description="Belum ada sesi evaluasi pada tahun arsip ini." />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</x-layouts.admin>
