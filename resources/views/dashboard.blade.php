@php
    $user = auth()->user();
    $tone = fn (?string $t) => $t === 'secondary' ? 'neutral' : $t;
    $barTone = fn (?string $t) => $t === 'secondary' ? 'bg-muted-foreground' : 'bg-'.$t;

    $chartItems = collect($distribution)
        ->map(fn ($count, $code) => ['label' => $code, 'value' => (int) $count])
        ->values()
        ->all();
    $chartTotal = array_sum(array_column($chartItems, 'value'));
@endphp

<x-layouts.admin title="Dashboard" group="Platform">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <x-ui.page-header
            :title="$user->isOpd() ? 'Dashboard '.($user->opd?->name ?? 'Unit Layanan') : 'Dashboard PEKPPP Mandiri'"
            :description="'Ringkasan hasil Penilaian Kinerja Pelayanan Publik Kabupaten Pasaman tahun '.$year.'.'" />
        <form method="GET" action="{{ route('dashboard') }}" data-auto-submit class="flex items-end gap-2">
            <x-ui.field label="Tahun Penilaian" name="tahun" class="w-40">
                <x-ui.select name="tahun">
                    @foreach ($availableYears as $y)
                        <option value="{{ $y }}" @selected($year === $y)>Tahun {{ $y }}</option>
                    @endforeach
                </x-ui.select>
            </x-ui.field>
            <x-ui.button type="submit" variant="soft" size="sm"><x-ui.icon name="refresh" class="h-4 w-4" /> Terapkan</x-ui.button>
        </form>
    </div>

    @if ($totalUnits === 0)
        <x-ui.alert variant="warning">
            Belum ada data evaluasi pada tahun {{ $year }}.
            @if ($user->isAdmin())
                Silakan buat sesi evaluasi melalui menu <strong>Master Data &rsaquo; Evaluasi Unit</strong>.
            @else
                Silakan hubungi Administrator.
            @endif
        </x-ui.alert>
    @endif

    {{-- Statistik utama --}}
    <section aria-label="Statistik utama" class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="card p-6">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h3 class="text-sm font-medium text-muted-foreground">Unit Terevaluasi</h3>
                    <p class="mt-2 text-2xl font-semibold tracking-tight tabular-nums">{{ $totalUnits }}</p>
                </div>
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-success-soft text-success"><x-ui.icon name="layers" class="h-5 w-5" /></span>
            </div>
            <p class="mt-3 text-xs text-muted-foreground">dari {{ $totalOpd }} unit aktif</p>
        </div>
        <div class="card p-6">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h3 class="text-sm font-medium text-muted-foreground">Rata-Rata Indeks IPP</h3>
                    <p class="mt-2 text-2xl font-semibold tracking-tight tabular-nums text-primary">{{ number_format($avgIpp, 3) }}</p>
                </div>
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-soft text-soft-foreground"><x-ui.icon name="trend-up" class="h-5 w-5" /></span>
            </div>
            <p class="mt-3 text-xs text-muted-foreground">skala 0,000 &ndash; 5,000</p>
        </div>
        <div class="card p-6">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h3 class="text-sm font-medium text-muted-foreground">Predikat Prima (A / A-)</h3>
                    <p class="mt-2 text-2xl font-semibold tracking-tight tabular-nums text-warning">{{ $primaCount }} Unit</p>
                </div>
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-warning-soft text-warning"><x-ui.icon name="star" class="h-5 w-5" /></span>
            </div>
            <p class="mt-3 text-xs text-muted-foreground">pelayanan prima &amp; sangat baik</p>
        </div>
        <div class="card p-6">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0 flex-1">
                    <h3 class="text-sm font-medium text-muted-foreground">Progres Isian F-01</h3>
                    <p class="mt-2 text-2xl font-semibold tracking-tight tabular-nums text-info">{{ $f01Progress }}%</p>
                </div>
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-info-soft text-info"><x-ui.icon name="edit" class="h-5 w-5" /></span>
            </div>
            <div class="progress mt-3 h-1.5"><div class="progress-bar bg-info" style="width: {{ $f01Progress }}%"></div></div>
            <p class="mt-1.5 text-xs text-muted-foreground">{{ number_format($answeredCount) }} dari {{ number_format($expectedAnswers) }} butir</p>
        </div>
    </section>

    {{-- Kartu status unit (khusus user OPD) --}}
    @if ($myEvaluation)
        <div class="card border-l-4 border-success p-6">
            <h2 class="mb-4 flex items-center gap-2 font-semibold"><x-ui.icon name="info" class="h-4 w-4 text-success" /> Status Penilaian Unit Anda</h2>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
                <div>
                    <p class="text-xs text-muted-foreground">Unit Layanan</p>
                    <p class="font-semibold">{{ $myEvaluation->opd?->name ?? '—' }}</p>
                    <x-ui.badge variant="success" class="mt-1">{{ \App\Models\Opd::categories()[$myEvaluation->opd?->category] ?? $myEvaluation->opd?->category }}</x-ui.badge>
                </div>
                <div>
                    <p class="text-xs text-muted-foreground">Status</p>
                    <x-ui.badge :variant="$tone(\App\Models\Evaluation::statusTone($myEvaluation->status))">{{ \App\Models\Evaluation::statuses()[$myEvaluation->status] ?? $myEvaluation->status }}</x-ui.badge>
                </div>
                <div>
                    <p class="text-xs text-muted-foreground">Skor F-02 (75%)</p>
                    <p class="font-semibold tabular-nums">{{ number_format((float) $myEvaluation->score_f02, 3) }}</p>
                </div>
                <div>
                    <p class="text-xs text-muted-foreground">Skor F-03 (25%)</p>
                    <p class="font-semibold tabular-nums">{{ number_format((float) $myEvaluation->score_f03, 3) }}</p>
                </div>
                <div>
                    <p class="text-xs text-muted-foreground">Indeks IPP</p>
                    <p class="text-lg font-bold tabular-nums text-success">{{ number_format((float) $myEvaluation->final_score_ipp, 3) }}</p>
                    @if ($myEvaluation->category_ipp)
                        <x-ui.badge :variant="$tone($myEvaluation->categoryTone())">{{ $myEvaluation->category_ipp }} · {{ $myEvaluation->category_ipp_label }}</x-ui.badge>
                    @endif
                </div>
            </div>

            <div class="mt-4 flex flex-wrap gap-2 border-t pt-4">
                @if ($user->isOpd())
                    <a href="{{ route('f01.index') }}" class="btn btn-success btn-sm"><x-ui.icon name="edit" class="h-4 w-4" /> Buka Formulir F-01</a>
                @endif
                @if ($user->isAdmin())
                    <a href="{{ route('f02.index', ['evaluasi' => $myEvaluation->id]) }}" class="btn btn-primary btn-sm"><x-ui.icon name="check-circle" class="h-4 w-4" /> Nilai F-02</a>
                    <a href="{{ route('f03.index', ['evaluasi' => $myEvaluation->id]) }}" class="btn btn-warning btn-sm"><x-ui.icon name="message" class="h-4 w-4" /> Input F-03</a>
                @endif
                <a href="{{ route('peringkat.index', ['tahun' => $year]) }}" class="btn btn-outline btn-sm"><x-ui.icon name="star" class="h-4 w-4" /> Lihat Papan Peringkat</a>
            </div>
        </div>
    @endif

    {{-- Grafik & status --}}
    <section class="grid gap-4 lg:grid-cols-5">
        <x-ui.card class="lg:col-span-2" title="Distribusi Predikat IPP" description="Sebaran predikat unit pada tahun {{ $year }}.">
            @if ($chartTotal > 0)
                <x-ui.chart type="donut" :items="$chartItems" :size="220" totalLabel="Total Unit" aria="Distribusi predikat IPP" />
            @else
                <x-ui.empty icon="pie" title="Belum ada data predikat" description="Data akan muncul setelah penilaian unit selesai." />
            @endif
        </x-ui.card>

        <x-ui.card class="lg:col-span-3" title="Progres Status Evaluasi" description="{{ $totalUnits }} unit pada tahun {{ $year }}.">
            @if ($totalUnits === 0)
                <p class="text-sm text-muted-foreground">Belum ada data untuk ditampilkan.</p>
            @else
                <div class="space-y-3">
                    @foreach (\App\Models\Evaluation::statuses() as $statusKey => $statusLabel)
                        @php
                            $count = $statusCounts[$statusKey] ?? 0;
                            $percent = (int) round(($count / $totalUnits) * 100);
                        @endphp
                        <div>
                            <div class="mb-1 flex justify-between text-sm">
                                <span class="font-medium">{{ $statusLabel }}</span>
                                <span class="text-muted-foreground">{{ $count }} unit ({{ $percent }}%)</span>
                            </div>
                            <div class="progress h-2.5"><div class="progress-bar {{ $barTone(\App\Models\Evaluation::statusTone($statusKey)) }}" style="width: {{ $percent }}%"></div></div>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-ui.card>
    </section>

    {{-- Peringkat teratas --}}
    <div class="card">
        <div class="flex items-center justify-between gap-4 p-6 pb-4">
            <div>
                <h2 class="card-title">5 Peringkat Teratas Indeks IPP</h2>
                <p class="card-desc mt-1.5">Unit dengan capaian tertinggi tahun {{ $year }}.</p>
            </div>
            <a href="{{ route('peringkat.index', ['tahun' => $year]) }}" class="btn btn-outline btn-sm">Lihat Semua <x-ui.icon name="arrow-right" class="h-4 w-4" /></a>
        </div>
        <div class="overflow-x-auto border-t">
            <table class="table min-w-[720px]">
                <thead>
                    <tr>
                        <th class="text-center">Peringkat</th>
                        <th>Unit Layanan / Lokus</th>
                        <th class="hidden md:table-cell">Kategori</th>
                        <th class="hidden text-right lg:table-cell">F-02</th>
                        <th class="hidden text-right lg:table-cell">F-03</th>
                        <th class="text-right">Indeks IPP</th>
                        <th class="text-center">Predikat</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($topRankings as $index => $row)
                        <tr>
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
                                    <x-ui.badge :variant="$tone($row->categoryTone())">{{ $row->category_ipp }}</x-ui.badge>
                                    <span class="mt-1 block text-xs text-muted-foreground">{{ $row->category_ipp_label }}</span>
                                @else
                                    <span class="text-xs text-muted-foreground">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <x-ui.empty icon="star" title="Belum ada data evaluasi" description="Belum ada data evaluasi pada tahun {{ $year }}." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
