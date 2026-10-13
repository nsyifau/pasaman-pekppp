@php
    $tone = fn (?string $t) => $t === 'secondary' ? 'neutral' : $t;
    $statuses = \App\Models\Evaluation::statuses();
    $categories = \App\Models\Opd::categories();
    $weight = fn ($w) => rtrim(rtrim(number_format($w, 2), '0'), ',');
@endphp

<x-layouts.admin title="F-02 Penilaian" group="Instrumen PEKPPP">
    <x-ui.page-header title="Formulir F-02: Penilaian Per Butir Pertanyaan"
        description="Tinjau isian mandiri F-01 unit layanan (tampilan saja), lalu berikan skor 0-5 untuk setiap butir pertanyaan beserta catatan." />

    <div class="card p-5 sm:p-6">
        @include('partials.evaluation-selector', ['selectorRoute' => 'f02.index'])
    </div>

    @if (! $selectedEvaluation)
        <x-ui.alert variant="warning">Belum ada sesi evaluasi yang dipilih. Silakan pilih unit layanan pada daftar di atas.</x-ui.alert>
    @else
        {{-- Ringkasan --}}
        <div class="card border-l-4 border-primary p-6">
            <div class="grid items-center gap-5 lg:grid-cols-12">
                <div class="lg:col-span-5">
                    <div class="mb-2 flex flex-wrap gap-2">
                        <x-ui.badge variant="success" class="badge-kategori">{{ $categories[$selectedEvaluation->opd?->category] ?? $selectedEvaluation->opd?->category }}</x-ui.badge>
                        <x-ui.badge :variant="$tone(\App\Models\Evaluation::statusTone($selectedEvaluation->status))" class="badge-kategori">{{ $statuses[$selectedEvaluation->status] ?? $selectedEvaluation->status }}</x-ui.badge>
                        <x-ui.badge variant="neutral" class="badge-kategori">Tahun {{ $selectedEvaluation->year }}</x-ui.badge>
                    </div>
                    <h2 class="text-lg font-semibold">{{ $selectedEvaluation->opd?->name ?? '—' }}</h2>
                </div>
                <div class="lg:col-span-2 lg:border-l lg:pl-5">
                    <p class="text-xs text-muted-foreground">Skor F-02 (75%)</p>
                    <p class="text-2xl font-bold tabular-nums text-success">{{ number_format((float) $selectedEvaluation->score_f02, 3) }}</p>
                </div>
                <div class="lg:col-span-2 lg:border-l lg:pl-5">
                    <p class="text-xs text-muted-foreground">Skor F-03 (25%)</p>
                    <p class="text-2xl font-bold tabular-nums text-warning">{{ number_format((float) $selectedEvaluation->score_f03, 3) }}</p>
                </div>
                <div class="lg:col-span-3 lg:border-l lg:pl-5">
                    <p class="text-xs text-muted-foreground">Indeks Akhir IPP</p>
                    <p class="text-2xl font-bold tabular-nums text-primary">{{ number_format((float) $selectedEvaluation->final_score_ipp, 3) }}</p>
                    @if ($selectedEvaluation->category_ipp)
                        <x-ui.badge :variant="$tone($selectedEvaluation->categoryTone())">{{ $selectedEvaluation->category_ipp }} · {{ $selectedEvaluation->category_ipp_label }}</x-ui.badge>
                    @endif
                </div>
            </div>
        </div>

        {{-- Navigasi aspek + panel penilaian --}}
        <form id="f02Form" method="POST" action="{{ route('f02.store') }}">
            @csrf
            <input type="hidden" name="evaluation_id" value="{{ $selectedEvaluation->id }}">

            <x-ui.tabs id="f02aspek" variant="pill" aria-label="Aspek penilaian">
                <x-slot:tablist>
                    <div class="tabs-pill mb-4" role="tablist" aria-label="Aspek penilaian">
                        @foreach ($aspects as $asp)
                            <button type="button" role="tab" class="tab" id="f02aspek-tab-{{ $asp->id }}"
                                aria-controls="f02aspek-panel-{{ $asp->id }}"
                                aria-selected="{{ $loop->first ? 'true' : 'false' }}"
                                tabindex="{{ $loop->first ? '0' : '-1' }}">
                                <span class="min-w-0">{{ $asp->name }}</span>
                                <span class="tab-count shrink-0">{{ $asp->indicators->flatMap->f01Questions->count() }}</span>
                            </button>
                        @endforeach
                    </div>
                </x-slot:tablist>

                @foreach ($aspects as $asp)
                    <div role="tabpanel" id="f02aspek-panel-{{ $asp->id }}" aria-labelledby="f02aspek-tab-{{ $asp->id }}"
                        @if (! $loop->first) hidden @endif>
                        @include('f02._aspect', ['asp' => $asp])
                    </div>
                @endforeach
            </x-ui.tabs>

            <div class="card p-6">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <p class="text-sm text-muted-foreground">Skor F-02 dihitung dari rata-rata skor butir &rarr; indikator &rarr; aspek terbobot. IPP = F-02 (75%) + F-03 (25%).</p>
                    <div class="flex gap-2">
                        <a href="{{ route('laporan.berita_acara', $selectedEvaluation) }}" target="_blank" rel="noopener" class="btn btn-soft"><x-ui.icon name="file-text" class="h-4 w-4" /> Cetak Berita Acara</a>
                        <a href="{{ route('f03.index', ['evaluasi' => $selectedEvaluation->id]) }}" class="btn btn-outline"><x-ui.icon name="message" class="h-4 w-4" /> Lanjut Input F-03</a>
                        <x-ui.button type="submit" variant="success"><x-ui.icon name="check-circle" class="h-4 w-4" /> Simpan Skor F-02</x-ui.button>
                    </div>
                </div>
            </div>
        </form>
    @endif
</x-layouts.admin>
