@php
    $tone = fn (?string $t) => $t === 'secondary' ? 'neutral' : $t;
    $statuses = \App\Models\Evaluation::statuses();
    $categories = \App\Models\Opd::categories();
    $rawValue = old('score_f03_manual', $selectedEvaluation?->score_f03_manual);
@endphp

<x-layouts.admin title="F-03 Penilaian Persepsi" group="Instrumen PEKPPP">
    <x-ui.page-header title="Formulir F-03: Penilaian Persepsi per Unit Layanan"
        description="Input satu nilai persepsi pengguna layanan per unit (skala 1-100) dengan bobot 25% terhadap Indeks Pelayanan Publik." />

    <div class="card p-5 sm:p-6">
        @if ($evaluations->isEmpty())
            <x-ui.empty icon="message" title="Belum ada unit yang mengisi F-01"
                description="Formulir F-03 hanya tersedia untuk unit layanan yang telah mengirim isian F-01." />
        @else
            @include('partials.evaluation-selector', ['selectorRoute' => 'f03.index'])
        @endif
    </div>

    @if ($evaluations->isEmpty())
        {{-- tanpa unit --}}
    @elseif (! $selectedEvaluation)
        <x-ui.alert variant="warning">Belum ada sesi evaluasi yang dipilih. Silakan pilih unit layanan pada daftar di atas.</x-ui.alert>
    @else
        {{-- Ringkasan --}}
        <div class="card border-l-4 border-warning p-6">
            <div class="grid items-center gap-5 lg:grid-cols-12">
                <div class="lg:col-span-5">
                    <div class="mb-2 flex flex-wrap gap-2">
                        <x-ui.badge variant="warning" class="badge-kategori">{{ $categories[$selectedEvaluation->opd?->category] ?? $selectedEvaluation->opd?->category }}</x-ui.badge>
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

        {{-- Formulir F-03 --}}
        <form id="f03Form" method="POST" action="{{ route('f03.store') }}">
            @csrf
            <input type="hidden" name="evaluation_id" value="{{ $selectedEvaluation->id }}">

            <div class="card">
                <div class="card-header gold-header-card rounded-t-xl">
                    <h2 class="font-semibold text-white"><x-ui.icon name="chart" class="inline h-4 w-4" /> Nilai Persepsi Pengguna Layanan</h2>
                    <p class="text-sm text-white/80">Satu nilai per unit layanan, skala 1 s.d. 100. Nilai ini mewakili hasil survei persepsi masyarakat terhadap unit tersebut.</p>
                </div>

                <div class="card-content grid gap-6 pt-6 lg:grid-cols-2">
                    <x-ui.field label="Nilai F-03 (1 - 100)" name="score_f03_manual" required
                        help="Masukkan nilai persepsi unit layanan antara 1 sampai 100.">
                        <x-ui.input id="score_f03_manual" type="number" name="score_f03_manual" min="1" max="100" step="0.01"
                            :value="$rawValue" required class="text-center text-lg font-bold" placeholder="Contoh: 82" />
                    </x-ui.field>

                    <div class="rounded-lg border border-warning/30 bg-warning-soft/60 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-warning">Pratinjau Konversi</p>
                        <p class="mt-1 text-sm text-muted-foreground">Nilai 1-100 dikonversi ke skala 0-5 (dibagi 20) sebelum dihitung ke Indeks IPP.</p>
                        <div class="mt-3 flex items-end gap-2">
                            <span id="previewNilai" class="text-3xl font-bold tabular-nums text-warning">—</span>
                            <span class="pb-1 text-sm text-muted-foreground">/ 5,000</span>
                        </div>
                        <p id="previewRumus" class="mt-1 text-xs text-muted-foreground">Masukkan nilai untuk melihat hasil konversi.</p>
                    </div>
                </div>

                <div class="card-footer justify-between">
                    <p class="text-xs text-muted-foreground">Indeks IPP dihitung ulang otomatis: F-02 (75%) + F-03 (25%).</p>
                    <div class="flex gap-2">
                        <a href="{{ route('f02.index', ['evaluasi' => $selectedEvaluation->id]) }}" class="btn btn-outline"><x-ui.icon name="arrow-left" class="h-4 w-4" /> Kembali ke F-02</a>
                        <x-ui.button type="submit" variant="warning"><x-ui.icon name="check-circle" class="h-4 w-4" /> Simpan Nilai F-03 &amp; Hitung IPP</x-ui.button>
                    </div>
                </div>
            </div>
        </form>
    @endif
</x-layouts.admin>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var input = document.getElementById('score_f03_manual');
            var nilai = document.getElementById('previewNilai');
            var rumus = document.getElementById('previewRumus');
            if (!input || !nilai) return;

            var render = function () {
                var v = parseFloat(input.value);
                if (isNaN(v) || v < 1 || v > 100) {
                    nilai.textContent = '—';
                    rumus.textContent = 'Masukkan nilai antara 1 sampai 100.';
                    return;
                }
                var konversi = (v / 20).toFixed(3);
                nilai.textContent = konversi.replace('.', ',');
                rumus.textContent = v + ' ÷ 20 = ' + konversi + ' (skala 0-5)';
            };

            input.addEventListener('input', render);
            render();
        });
    </script>
@endpush
