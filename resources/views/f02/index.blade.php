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

        {{-- Navigasi aspek --}}
        <div class="card p-2">
            <div class="flex flex-wrap gap-1" role="tablist" aria-label="Aspek penilaian">
                @foreach ($aspects as $asp)
                    <button type="button" role="tab" data-f02-tab="{{ $asp->id }}"
                        @class(['btn btn-sm', 'btn-primary' => $loop->first, 'btn-ghost' => ! $loop->first])>
                        {{ $asp->name }}
                    </button>
                @endforeach
            </div>
        </div>

        <form id="f02Form" method="POST" action="{{ route('f02.store') }}">
            @csrf
            <input type="hidden" name="evaluation_id" value="{{ $selectedEvaluation->id }}">

            @forelse ($aspects as $asp)
                @php
                    $aspQuestions = $asp->indicators->flatMap->f01Questions;
                    $aspScored = $aspQuestions->filter(fn ($q) => isset($scores[$q->id]))->count();
                    $aspTotal = $aspQuestions->count();
                @endphp
                <div class="card" data-f02-panel="{{ $asp->id }}" @if (! $loop->first) hidden @endif>
                    <div class="card-header gov-header-card rounded-t-xl">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wide text-white/80">Aspek Penilaian</p>
                                <h2 class="font-semibold text-white">{{ $asp->name }}</h2>
                                <p class="text-sm text-white/80">Bobot aspek: <strong>{{ $weight($asp->weight) }}%</strong> dari total F-02</p>
                            </div>
                            <span class="badge bg-white/20 text-white">{{ $aspScored }}/{{ $aspTotal }} butir dinilai</span>
                        </div>
                    </div>

                    <div class="card-content space-y-6 pt-6">
                        @forelse ($asp->indicators as $ind)
                            <div @class(['border-t pt-6' => ! $loop->first])>
                                <div class="mb-4 flex flex-wrap items-center gap-2">
                                    <x-ui.badge variant="success">Indikator {{ $ind->number }}</x-ui.badge>
                                    <x-ui.badge variant="outline">Kode {{ $ind->code }}</x-ui.badge>
                                    <h3 class="text-sm font-semibold">{{ $ind->name }}</h3>
                                </div>

                                @if (! empty ($ind->rubric_scales))
                                    <div class="rubric-box mb-4">
                                        <p class="mb-2 text-xs font-bold text-info"><x-ui.icon name="file-text" class="inline h-4 w-4" /> Panduan Rubrik Penilaian (Keputusan Bupati Pasaman):</p>
                                        <div class="grid gap-2 md:grid-cols-2 lg:grid-cols-3">
                                            @foreach ($ind->rubric_scales as $scale => $desc)
                                                <div class="flex items-start gap-2 text-sm">
                                                    <x-ui.badge variant="primary" class="shrink-0">{{ $scale }}</x-ui.badge>
                                                    <span>{{ $desc }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <div class="space-y-4">
                                    @forelse ($ind->f01Questions as $q)
                                        @php
                                            $ans = $f01Answers->get($q->id);
                                            $answer = $ans?->answer_value;
                                            $answerText = is_array($answer)
                                                ? implode(', ', array_map('strval', $answer))
                                                : (string) ($answer ?? '');
                                        @endphp
                                        <div class="rounded-lg border">
                                            <div class="flex items-start gap-2.5 border-b bg-muted/20 px-4 py-3">
                                                <x-ui.badge variant="primary" class="mt-0.5 shrink-0">{{ $q->question_code }}</x-ui.badge>
                                                <p class="text-sm font-medium leading-relaxed">{{ $q->question_text }}</p>
                                            </div>

                                            <div class="grid gap-4 p-4 lg:grid-cols-12">
                                                {{-- Isian F-01 read-only --}}
                                                <div class="lg:col-span-7">
                                                    <p class="mb-1 text-xs font-bold text-success"><x-ui.icon name="eye" class="inline h-3.5 w-3.5" /> Jawaban F-01 Unit Layanan</p>
                                                    @if ($answerText !== '')
                                                        <p class="rounded bg-success-soft/50 px-3 py-2 text-sm font-medium">{{ $answerText }}</p>
                                                    @else
                                                        <p class="text-sm italic text-muted-foreground">Belum diisi unit layanan.</p>
                                                    @endif

                                                    @if (! empty ($ans?->drive_link))
                                                        <a href="{{ $ans->drive_link }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline btn-sm mt-2 truncate text-success">
                                                            <x-ui.icon name="external" class="h-4 w-4" /> Buka Bukti Dukung (Google Drive)
                                                        </a>
                                                    @else
                                                        <p class="mt-1 text-xs italic text-muted-foreground">Belum ada tautan Google Drive.</p>
                                                    @endif
                                                </div>

                                                {{-- Skor penilai --}}
                                                <div class="lg:col-span-5">
                                                    <div class="rounded-lg border border-warning/30 bg-warning-soft/60 p-3">
                                                        <x-ui.field label="Skor (0-5)" name="scores_{{ $q->id }}">
                                                            <x-ui.select name="scores[{{ $q->id }}]" class="text-center font-bold">
                                                                @for ($i = 0; $i <= 5; $i++)
                                                                    <option value="{{ $i }}" @selected((int) ($scores[$q->id] ?? 0) === $i)>{{ $i }}</option>
                                                                @endfor
                                                            </x-ui.select>
                                                        </x-ui.field>
                                                        <x-ui.field label="Catatan" name="notes_{{ $q->id }}" class="mt-3">
                                                            <x-ui.textarea name="notes[{{ $q->id }}]" rows="2" class="input-sm"
                                                                placeholder="Catatan/rekomendasi untuk butir ini...">{{ $evaluatorNotes[$q->id] ?? '' }}</x-ui.textarea>
                                                        </x-ui.field>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-sm italic text-muted-foreground">Tidak ada sub-pertanyaan F-01 pada indikator ini.</p>
                                    @endforelse
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-muted-foreground">Belum ada indikator pada aspek ini.</p>
                        @endforelse
                    </div>
                </div>
            @empty
                <x-ui.alert variant="info">Belum ada aspek penilaian yang memiliki bobot F-02.</x-ui.alert>
            @endforelse

            <div class="card p-6">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <p class="text-sm text-muted-foreground">Skor F-02 dihitung dari rata-rata skor butir &rarr; indikator &rarr; aspek terbobot. IPP = F-02 (75%) + F-03 (25%).</p>
                    <div class="flex gap-2">
                        <a href="{{ route('f03.index', ['evaluasi' => $selectedEvaluation->id]) }}" class="btn btn-outline"><x-ui.icon name="message" class="h-4 w-4" /> Lanjut Input F-03</a>
                        <x-ui.button type="submit" variant="success"><x-ui.icon name="check-circle" class="h-4 w-4" /> Simpan Skor F-02</x-ui.button>
                    </div>
                </div>
            </div>
        </form>
    @endif
</x-layouts.admin>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var tabs = document.querySelectorAll('[data-f02-tab]');
            var panels = document.querySelectorAll('[data-f02-panel]');
            tabs.forEach(function (tab) {
                tab.addEventListener('click', function () {
                    tabs.forEach(function (t) { t.classList.remove('btn-primary'); t.classList.add('btn-ghost'); });
                    tab.classList.remove('btn-ghost'); tab.classList.add('btn-primary');
                    panels.forEach(function (p) { p.hidden = p.dataset.f02Panel !== tab.dataset.f02Tab; });
                });
            });
        });
    </script>
@endpush
