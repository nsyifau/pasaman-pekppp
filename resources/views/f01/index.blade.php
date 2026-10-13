@php
    $tone = fn (?string $t) => $t === 'secondary' ? 'neutral' : $t;
    $statuses = \App\Models\Evaluation::statuses();
    $categories = \App\Models\Opd::categories();
    $weight = fn ($w) => rtrim(rtrim(number_format($w, 2), '0'), ',');
@endphp

<x-layouts.admin title="F-01 Penilaian Mandiri" group="Instrumen PEKPPP">
    <x-ui.page-header title="Formulir F-01: Penilaian Mandiri Pelayanan Publik"
        description="Isi seluruh butir instrumen secara mandiri dan lampirkan tautan Google Drive bukti dukung." />

    @if (! $evaluation)
        <x-ui.alert variant="warning" title="Unit Belum Ditetapkan">
            Akun pengguna Anda belum dikaitkan dengan Unit Layanan / Lokus tertentu.
            Silakan hubungi Administrator untuk menghubungkan akun Anda ke OPD terkait.
        </x-ui.alert>
    @else
        {{-- Identitas & progres --}}
        <div class="card border-l-4 border-success p-6">
            <div class="grid items-center gap-5 lg:grid-cols-12">
                <div class="lg:col-span-7">
                    <div class="mb-2 flex flex-wrap gap-2">
                        <x-ui.badge variant="success" class="badge-kategori">{{ $categories[$evaluation->opd?->category] ?? $evaluation->opd?->category }}</x-ui.badge>
                        <x-ui.badge :variant="$tone(\App\Models\Evaluation::statusTone($evaluation->status))" class="badge-kategori">{{ $statuses[$evaluation->status] ?? $evaluation->status }}</x-ui.badge>
                        <x-ui.badge variant="neutral" class="badge-kategori">Tahun {{ $evaluation->year }}</x-ui.badge>
                    </div>
                    <h2 class="text-lg font-semibold">{{ $evaluation->opd?->name ?? '—' }}</h2>
                </div>
                <div class="lg:col-span-5">
                    <div class="rounded-lg border bg-muted/40 p-4">
                        <div class="mb-2 flex items-center justify-between">
                            <span class="text-sm font-semibold"><x-ui.icon name="chart" class="inline h-4 w-4 text-success" /> Progres Pengisian F-01</span>
                            <span class="font-bold text-success tabular-nums">{{ $stats['answered'] }}/{{ $stats['total'] }} ({{ $stats['percentage'] }}%)</span>
                        </div>
                        <div class="progress h-2.5"><div class="progress-bar bg-success" style="width: {{ $stats['percentage'] }}%"></div></div>
                        <div class="mt-2 flex justify-between text-xs text-muted-foreground">
                            <span>{{ $stats['drive_count'] }} tautan Drive tersimpan</span>
                            <span>Lampirkan bukti dukung tiap butir</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if (in_array($evaluation->status, ['submitted', 'in_review', 'verified', 'completed'], true))
            <x-ui.alert variant="info">
                Formulir ini sudah <strong>{{ strtolower($statuses[$evaluation->status] ?? $evaluation->status) }}</strong>.
                Perubahan yang Anda simpan tetap tercatat, namun penilaian oleh Tim Penilai mengacu pada data terakhir yang dikirim.
            </x-ui.alert>
        @endif

        <form id="f01Form" method="POST" action="{{ route('f01.save_draft') }}">
            @csrf

            <div class="card">
                <div class="card-header">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <h2 class="card-title">Butir Instrumen per Aspek</h2>
                            <p class="card-desc">Jawab setiap butir, lalu lampirkan tautan Google Drive bukti dukungnya.</p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <x-ui.button type="submit" variant="outline" size="sm"><x-ui.icon name="check" class="h-4 w-4" /> Simpan Draft</x-ui.button>
                            <button type="button" class="btn btn-success btn-sm" onclick="kirimF01()"><x-ui.icon name="logout" class="h-4 w-4" /> Kirim ke Penilai</button>
                        </div>
                    </div>
                </div>

                <div class="card-content space-y-6 pt-6">
                    @foreach ($aspects as $asp)
                        @php
                            $aspQuestions = $asp->indicators->flatMap->f01Questions;
                            $aspTotal = $aspQuestions->count();
                            $aspAnswered = $aspQuestions->filter(fn ($q) => ! empty($answers[$q->id] ?? null))->count();
                            $aspPercent = $aspTotal > 0 ? (int) round(($aspAnswered / $aspTotal) * 100) : 0;
                        @endphp
                        <section id="aspek-{{ $asp->code }}" class="overflow-hidden rounded-xl border">
                            <header class="flex flex-wrap items-start justify-between gap-3 border-b bg-muted/40 px-5 py-4">
                                <div class="min-w-0">
                                    <h3 class="text-base font-semibold">{{ $asp->name }}</h3>
                                    <p class="text-sm text-muted-foreground">Jawab seluruh butir indikator di bawah ini beserta bukti dukungnya.</p>
                                </div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <x-ui.badge variant="outline">{{ $asp->indicators->count() }} Indikator</x-ui.badge>
                                    @if ($asp->weight > 0)
                                        <x-ui.badge variant="success">Bobot F-02: {{ $weight($asp->weight) }}%</x-ui.badge>
                                    @endif
                                    <x-ui.badge :variant="$aspPercent === 100 ? 'success' : 'neutral'">{{ $aspAnswered }}/{{ $aspTotal }} butir</x-ui.badge>
                                </div>
                            </header>

                            <div class="space-y-5 p-5">
                                @forelse ($asp->indicators as $ind)
                                    <article class="rounded-lg border">
                                        <div class="flex flex-wrap items-center justify-between gap-2 border-b bg-muted/20 px-4 py-3">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <x-ui.badge variant="success">Indikator {{ $ind->number }}</x-ui.badge>
                                                <x-ui.badge variant="outline">Kode: {{ $ind->code }}</x-ui.badge>
                                            </div>
                                            <x-ui.badge variant="outline">{{ $ind->f01Questions->count() }} Butir</x-ui.badge>
                                        </div>

                                        <div class="border-b px-4 py-3">
                                            <h4 class="font-semibold leading-snug">{{ $ind->name }}</h4>
                                            @if ($ind->description)
                                                <p class="mt-1 text-sm text-muted-foreground">{{ $ind->description }}</p>
                                            @endif
                                        </div>

                                        <div class="space-y-4 bg-muted/10 p-4">
                                            @forelse ($ind->f01Questions as $q)
                                                @php
                                                    $saved = $answers[$q->id] ?? null;
                                                    $savedIsArray = is_array($saved);
                                                    $savedText = $savedIsArray ? implode(', ', $saved) : (string) ($saved ?? '');
                                                    $savedList = $savedIsArray ? array_map('strval', $saved) : [];
                                                    $link = $driveLinks[$q->id] ?? '';
                                                @endphp
                                                <div class="rounded-lg border bg-card p-4">
                                                    {{-- Pertanyaan --}}
                                                    <div class="flex items-start gap-2.5">
                                                        <x-ui.badge variant="primary" class="mt-0.5 shrink-0">{{ $q->question_code }}</x-ui.badge>
                                                        <p class="font-medium leading-relaxed">{{ $q->question_text }}</p>
                                                    </div>

                                                    {{-- Jawaban --}}
                                                    <div class="mt-4">
                                                        @if ($q->question_type === 'radio' && ! empty($q->options))
                                                            <div class="flex flex-wrap gap-2">
                                                                @foreach ($q->options as $opt)
                                                                    <label class="inline-flex cursor-pointer items-center gap-2 rounded-md border px-3 py-1.5 text-sm transition-colors hover:bg-accent has-[:checked]:border-primary has-[:checked]:bg-soft has-[:checked]:text-soft-foreground">
                                                                        <input type="radio" name="answers[{{ $q->id }}]" value="{{ $opt }}" @checked($savedText === (string) $opt)>
                                                                        <span>{{ $opt }}</span>
                                                                    </label>
                                                                @endforeach
                                                            </div>
                                                        @elseif ($q->question_type === 'checkbox' && ! empty($q->options))
                                                            <div class="grid gap-2 sm:grid-cols-2">
                                                                @foreach ($q->options as $opt)
                                                                    <label class="inline-flex cursor-pointer items-center gap-2 rounded-md border px-3 py-1.5 text-sm transition-colors hover:bg-accent has-[:checked]:border-primary has-[:checked]:bg-soft has-[:checked]:text-soft-foreground">
                                                                        <input type="checkbox" name="answers[{{ $q->id }}][]" value="{{ $opt }}" @checked(in_array((string) $opt, $savedList, true))>
                                                                        <span>{{ $opt }}</span>
                                                                    </label>
                                                                @endforeach
                                                            </div>
                                                        @elseif ($q->question_type === 'number')
                                                            <x-ui.input type="number" step="any" name="answers[{{ $q->id }}]" :value="$savedText" class="max-w-[16rem]" placeholder="Isikan angka/jumlah..." />
                                                        @else
                                                            <x-ui.textarea name="answers[{{ $q->id }}]" rows="2" placeholder="Tuliskan keterangan jawaban kondisi riil unit Anda...">{{ $savedText }}</x-ui.textarea>
                                                        @endif
                                                        @if ($q->help_text)
                                                            <p class="field-help">{{ $q->help_text }}</p>
                                                        @endif
                                                    </div>

                                                    {{-- Bukti dukung --}}
                                                    <div class="mt-4 rounded-lg border border-success/25 bg-success-soft/40 p-3">
                                                        <label class="label text-success" for="drive-{{ $q->id }}">
                                                            <x-ui.icon name="external" class="inline h-3.5 w-3.5" /> Tautan Google Drive Bukti Dukung
                                                        </label>
                                                        <div class="mt-1.5 flex flex-col gap-2 sm:flex-row">
                                                            <x-ui.input id="drive-{{ $q->id }}" type="url" name="drive_links[{{ $q->id }}]" :value="$link" class="input-sm flex-1" placeholder="https://drive.google.com/drive/folders/..." />
                                                            @if (! empty($link))
                                                                <a href="{{ $link }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline btn-sm shrink-0 text-success">
                                                                    <x-ui.icon name="external" class="h-4 w-4" /> Buka
                                                                </a>
                                                            @endif
                                                        </div>
                                                        @if ($q->evidence_hint)
                                                            <p class="field-help mt-1.5 italic">Bukti dukung yang diminta: {{ $q->evidence_hint }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            @empty
                                                <p class="text-sm text-muted-foreground">Belum ada butir pertanyaan pada indikator ini.</p>
                                            @endforelse
                                        </div>
                                    </article>
                                @empty
                                    <x-ui.alert variant="info">Belum ada indikator pada aspek ini.</x-ui.alert>
                                @endforelse
                            </div>
                        </section>
                    @endforeach
                </div>

                <div class="card-footer justify-between">
                    <p class="text-xs text-muted-foreground">Simpan draft secara berkala. Tombol <strong>Kirim ke Penilai</strong> mengunci isian untuk dinilai.</p>
                    <div class="flex flex-wrap gap-2">
                        <x-ui.button type="submit" variant="outline"><x-ui.icon name="check" class="h-4 w-4" /> Simpan Draft</x-ui.button>
                        <button type="button" class="btn btn-success" onclick="kirimF01()"><x-ui.icon name="logout" class="h-4 w-4" /> Kirim ke Penilai</button>
                    </div>
                </div>
            </div>
        </form>
    @endif
</x-layouts.admin>

@push('scripts')
    @if ($evaluation)
        <script>
            function kirimF01() {
                var run = function () {
                    window.App.confirm({
                        title: 'Kirim Formulir F-01?',
                        description: 'Pastikan seluruh isian dan tautan bukti dukung sudah benar. Isian akan dikunci untuk dinilai Tim Penilai.',
                        confirm: 'Ya, kirim sekarang'
                    }).then(function (ok) {
                        if (ok === false) return;
                        var form = document.getElementById('f01Form');
                        form.action = @json(route('f01.submit'));
                        form.submit();
                    });
                };
                if (window.App && window.App.confirm) { run(); } else { setTimeout(run, 60); }
            }

            // Simpan posisi aspek terakhir ke URL.
            document.querySelectorAll('[id^="aspek-"]').forEach(function (sec) {
                sec.addEventListener('click', function () {
                    var url = new URL(window.location.href);
                    url.searchParams.set('aspek', sec.id.replace('aspek-', ''));
                    window.history.replaceState({}, '', url);
                });
            });
        </script>
    @endif
@endpush
