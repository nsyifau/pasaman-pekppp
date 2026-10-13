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

        @if ($locked)
            <x-ui.alert variant="info" title="Formulir terkunci">
                Formulir ini sudah <strong>{{ strtolower($statuses[$evaluation->status] ?? $evaluation->status) }}</strong>
                dan tidak dapat diisi atau diperbarui lagi.
                @if ($evaluation->submitted_at)
                    Dikirim pada {{ $evaluation->submitted_at->timezone(config('app.timezone'))->format('d/m/Y H:i') }}.
                @endif
            </x-ui.alert>
        @endif

        <form id="f01Form" method="POST" action="{{ route('f01.save_draft') }}">
            @csrf
            <fieldset class="min-w-0 border-0 p-0" @disabled($locked)>

            <div class="card">
                <div class="card-header">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <h2 class="card-title">Butir Instrumen per Aspek</h2>
                            <p class="card-desc">
                                @if ($locked)
                                    Isian di bawah ini hanya dapat dilihat. Perubahan sudah dikunci untuk penilaian.
                                @else
                                    Jawab setiap butir, lalu lampirkan tautan Google Drive bukti dukungnya.
                                @endif
                            </p>
                        </div>
                        @unless ($locked)
                            <div class="flex flex-wrap gap-2">
                                <x-ui.button type="submit" variant="outline" size="sm"><x-ui.icon name="check" class="h-4 w-4" /> Simpan Draft</x-ui.button>
                                <button type="submit" class="btn btn-success btn-sm" formaction="{{ route('f01.submit') }}" data-f01-submit>
                                    <x-ui.icon name="logout" class="h-4 w-4" /> Kirim ke Penilai
                                </button>
                            </div>
                        @endunless
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
                                                <div class="rounded-lg border bg-card p-4 transition-colors"
                                                    data-question-item="{{ $q->id }}"
                                                    data-question-code="{{ $q->question_code }}"
                                                    data-indicator="{{ $ind->number }}"
                                                    data-drive-required="{{ $q->requires_drive_link ? '1' : '0' }}">
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
                                                                        <input type="radio" name="answers[{{ $q->id }}]" value="{{ $opt }}" data-f01-answer @checked($savedText === (string) $opt)>
                                                                        <span>{{ $opt }}</span>
                                                                    </label>
                                                                @endforeach
                                                            </div>
                                                        @elseif ($q->question_type === 'checkbox' && ! empty($q->options))
                                                            <div class="grid gap-2 sm:grid-cols-2">
                                                                @foreach ($q->options as $opt)
                                                                    <label class="inline-flex cursor-pointer items-center gap-2 rounded-md border px-3 py-1.5 text-sm transition-colors hover:bg-accent has-[:checked]:border-primary has-[:checked]:bg-soft has-[:checked]:text-soft-foreground">
                                                                        <input type="checkbox" name="answers[{{ $q->id }}][]" value="{{ $opt }}" data-f01-answer @checked(in_array((string) $opt, $savedList, true))>
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
                                                            <x-ui.input id="drive-{{ $q->id }}" type="text" inputmode="url" name="drive_links[{{ $q->id }}]" :value="$link" data-f01-drive class="input-sm flex-1" placeholder="https://drive.google.com/drive/folders/..." />
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
                    <p class="text-xs text-muted-foreground">
                        @if ($locked)
                            Formulir sudah dikirim ke Tim Penilai. Isian tidak dapat diubah dari akun unit layanan.
                        @else
                            Simpan draft secara berkala. Tombol <strong>Kirim ke Penilai</strong> mengunci isian untuk dinilai.
                        @endif
                    </p>
                    @unless ($locked)
                        <div class="flex flex-wrap gap-2">
                            <x-ui.button type="submit" variant="outline"><x-ui.icon name="check" class="h-4 w-4" /> Simpan Draft</x-ui.button>
                            <button type="submit" class="btn btn-success" formaction="{{ route('f01.submit') }}" data-f01-submit>
                                <x-ui.icon name="logout" class="h-4 w-4" /> Kirim ke Penilai
                            </button>
                        </div>
                    @endunless
                </div>
            </div>
            </fieldset>
        </form>

        @unless ($locked)
            {{-- Modal: daftar butir yang belum lengkap --}}
            <x-ui.modal id="f01IncompleteModal" title="Isian F-01 Belum Lengkap" icon="info" tone="warning" size="lg"
                description="Seluruh butir wajib dijawab dan dilengkapi tautan Google Drive bukti dukung sebelum dikirim ke penilai.">
                <div class="rounded-lg border border-warning/30 bg-warning-soft/60 p-4">
                    <p id="f01IncompleteSummary" class="text-sm font-semibold"></p>
                    <p class="mt-1 text-xs text-muted-foreground">Periksa daftar butir di bawah ini, lalu lengkapi isiannya.</p>
                </div>
                <ul id="f01IncompleteList" class="mt-4 max-h-64 space-y-2 overflow-y-auto pr-1 text-sm"></ul>
                <p id="f01IncompleteMore" class="mt-2 hidden text-xs text-muted-foreground"></p>

                <x-slot:footer>
                    <x-ui.button variant="outline" data-modal-close>Tutup</x-ui.button>
                    <x-ui.button variant="warning" data-f01-goto-first data-modal-close>
                        <x-ui.icon name="pointer" class="h-4 w-4" /> Lihat butir pertama
                    </x-ui.button>
                </x-slot:footer>
            </x-ui.modal>

            {{-- Modal: waktu tunggu proses simpan/kirim --}}
            <dialog id="f01WaitingModal" class="modal max-w-md" data-locked aria-labelledby="f01WaitingTitle"
                aria-live="assertive" aria-busy="true">
                <div class="flex flex-col items-center gap-3 px-8 py-8 text-center">
                    <span class="spinner h-10 w-10 text-success" aria-hidden="true"></span>
                    <h2 id="f01WaitingTitle" class="text-lg font-semibold">Mengirim formulir ke penilai...</h2>
                    <p id="f01WaitingStep" class="text-sm text-muted-foreground">Memvalidasi kelengkapan isian...</p>
                    <div class="progress progress-indeterminate mt-1 h-2 w-full">
                        <div class="progress-bar bg-success"></div>
                    </div>
                    <p class="text-xs text-muted-foreground">Mohon tunggu, jangan menutup halaman ini.</p>
                    <div id="f01WaitingFallback" class="hidden w-full space-y-2 pt-2">
                        <p class="text-xs font-medium text-danger">Prosesnya lebih lama dari biasanya. Silakan coba lagi atau muat ulang halaman.</p>
                        <div class="flex justify-center gap-2">
                            <x-ui.button variant="outline" size="sm" data-f01-retry>Coba lagi</x-ui.button>
                            <x-ui.button variant="soft" size="sm" data-f01-reload>Muat ulang halaman</x-ui.button>
                        </div>
                    </div>
                </div>
            </dialog>
        @endunless
    @endif
</x-layouts.admin>

@push('scripts')
    @if ($evaluation)
        <script>
            (function () {
                var form = document.getElementById('f01Form');
                if (!form) return;

                var confirmSubmit = @json(! $locked);
                var incompleteModal = document.getElementById('f01IncompleteModal');
                var waitingModal = document.getElementById('f01WaitingModal');
                var waitingTitle = waitingModal ? waitingModal.querySelector('#f01WaitingTitle') : null;
                var waitingStep = waitingModal ? waitingModal.querySelector('#f01WaitingStep') : null;
                var waitingFallback = document.getElementById('f01WaitingFallback');
                var incompleteSummary = document.getElementById('f01IncompleteSummary');
                var incompleteList = document.getElementById('f01IncompleteList');
                var incompleteMore = document.getElementById('f01IncompleteMore');

                var MAX_LISTED = 12;
                var WAIT_STEPS = [
                    'Memvalidasi kelengkapan isian...',
                    'Mengunggah tautan bukti dukung...',
                    'Menyimpan jawaban Anda...',
                    'Mengunci formulir & memberi tahu penilai...'
                ];
                var waitingTimer = null;
                var stepTimer = null;

                function isFilled(el) {
                    if (!el) return false;
                    if (el.type === 'checkbox' || el.type === 'radio') return el.checked;
                    return String(el.value || '').trim() !== '';
                }

                /* Kumpulkan butir yang belum lengkap berdasarkan isian form saat ini. */
                function collectIncomplete() {
                    var unanswered = [];
                    var missingDrive = [];

                    form.querySelectorAll('[data-question-item]').forEach(function (item) {
                        var code = item.getAttribute('data-question-code') || '';
                        var indicator = item.getAttribute('data-indicator') || '';
                        var driveRequired = item.getAttribute('data-drive-required') === '1';

                        var answerFilled = Array.prototype.some.call(
                            item.querySelectorAll('[data-f01-answer]'),
                            isFilled
                        );

                        if (!answerFilled) {
                            unanswered.push({ code: code, indicator: indicator });
                        }

                        if (driveRequired) {
                            var drive = item.querySelector('[data-f01-drive]');
                            if (!isFilled(drive)) {
                                missingDrive.push({ code: code, indicator: indicator });
                            }
                        }
                    });

                    return { unanswered: unanswered, missingDrive: missingDrive };
                }

                function renderIncomplete(data) {
                    var parts = [];
                    if (data.unanswered.length) parts.push(data.unanswered.length + ' butir belum dijawab');
                    if (data.missingDrive.length) parts.push(data.missingDrive.length + ' butir belum memiliki tautan Google Drive');
                    if (incompleteSummary) incompleteSummary.textContent = 'Masih ada ' + parts.join(' dan ') + '.';

                    var seen = {};
                    var rows = [];
                    data.unanswered.concat(data.missingDrive).forEach(function (entry) {
                        var key = entry.code + '|' + entry.indicator;
                        if (seen[key]) return;
                        seen[key] = true;
                        rows.push(entry);
                    });

                    if (incompleteList) {
                        incompleteList.innerHTML = '';
                        rows.slice(0, MAX_LISTED).forEach(function (entry) {
                            var li = document.createElement('li');
                            li.className = 'flex items-center justify-between gap-3 rounded-md border bg-muted/30 px-3 py-2';
                            li.innerHTML = '<span class="font-medium">Butir ' + entry.code + '</span>' +
                                '<span class="text-xs text-muted-foreground">Indikator ' + (entry.indicator || '—') + '</span>';
                            incompleteList.appendChild(li);
                        });
                    }

                    if (incompleteMore) {
                        if (rows.length > MAX_LISTED) {
                            incompleteMore.textContent = 'dan ' + (rows.length - MAX_LISTED) + ' butir lainnya.';
                            incompleteMore.classList.remove('hidden');
                        } else {
                            incompleteMore.classList.add('hidden');
                        }
                    }
                }

                /* Tandai dan fokuskan butir pertama yang belum lengkap. */
                function focusFirstIncomplete() {
                    var item = form.querySelector('[data-question-item].f01-incomplete');
                    if (!item) return;
                    item.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    var input = item.querySelector('[data-f01-answer], [data-f01-drive]');
                    if (input && input.focus) {
                        try { input.focus({ preventScroll: true }); } catch (e) { input.focus(); }
                    }
                }

                /* Bersihkan penanda butir belum lengkap saat isian berubah. */
                form.addEventListener('input', function (event) {
                    var item = event.target.closest ? event.target.closest('[data-question-item]') : null;
                    if (item) item.classList.remove('f01-incomplete');
                });
                form.addEventListener('change', function (event) {
                    var item = event.target.closest ? event.target.closest('[data-question-item]') : null;
                    if (item) item.classList.remove('f01-incomplete');
                });

                function markIncomplete(data) {
                    var codes = {};
                    data.unanswered.concat(data.missingDrive).forEach(function (entry) {
                        codes[entry.code] = true;
                    });
                    form.querySelectorAll('[data-question-item]').forEach(function (item) {
                        var code = item.getAttribute('data-question-code');
                        item.classList.toggle('f01-incomplete', !!codes[code]);
                    });
                }

                function showWaiting(mode) {
                    if (!waitingModal) return;
                    if (waitingTitle) {
                        waitingTitle.textContent = mode === 'draft'
                            ? 'Menyimpan draft...'
                            : 'Mengirim formulir ke penilai...';
                    }
                    if (waitingStep) {
                        waitingStep.textContent = mode === 'draft'
                            ? 'Menyimpan isian Anda...'
                            : WAIT_STEPS[0];
                    }
                    if (waitingFallback) waitingFallback.classList.add('hidden');

                    if (mode !== 'draft') {
                        var index = 0;
                        stepTimer = window.setInterval(function () {
                            index = Math.min(index + 1, WAIT_STEPS.length - 1);
                            if (waitingStep) waitingStep.textContent = WAIT_STEPS[index];
                        }, 2200);
                    }

                    waitingTimer = window.setTimeout(function () {
                        if (waitingFallback) waitingFallback.classList.remove('hidden');
                    }, 45000);

                    if (waitingModal.showModal && !waitingModal.open) waitingModal.showModal();
                }

                function clearWaitingTimers() {
                    if (waitingTimer) { window.clearTimeout(waitingTimer); waitingTimer = null; }
                    if (stepTimer) { window.clearInterval(stepTimer); stepTimer = null; }
                }

                if (waitingModal) {
                    waitingModal.addEventListener('close', clearWaitingTimers);
                    var retry = waitingModal.querySelector('[data-f01-retry]');
                    if (retry) retry.addEventListener('click', function () { window.location.reload(); });
                    var reload = waitingModal.querySelector('[data-f01-reload]');
                    if (reload) reload.addEventListener('click', function () { window.location.reload(); });
                }

                if (incompleteModal) {
                    var gotoBtn = incompleteModal.querySelector('[data-f01-goto-first]');
                    if (gotoBtn) gotoBtn.addEventListener('click', function () {
                        window.setTimeout(focusFirstIncomplete, 220);
                    });
                }

                /*
                 * Tampilkan modal waktu tunggu LEBIH DULU, baru kirim form secara native.
                 * Dua requestAnimationFrame menjamin modal sudah tergambar (paint) sebelum
                 * form.submit() dipanggil, sehingga modal tetap terlihat selama server
                 * memproses (mis. saat jaringan lambat).
                 */
                var submitting = false;

                function beginSubmit(submitter, mode) {
                    if (submitting) return;
                    submitting = true;

                    showWaiting(mode);

                    if (submitter && submitter.getAttribute('formaction')) {
                        form.setAttribute('action', submitter.getAttribute('formaction'));
                    }

                    form.querySelectorAll('button[type="submit"]').forEach(function (btn) {
                        btn.disabled = true;
                    });

                    window.requestAnimationFrame(function () {
                        window.requestAnimationFrame(function () {
                            form.submit();
                        });
                    });
                }

                form.addEventListener('submit', function (event) {
                    event.preventDefault();

                    if (submitting) return;

                    var submitter = event.submitter;
                    var isKirim = !!(submitter && submitter.hasAttribute('data-f01-submit'));

                    if (confirmSubmit && isKirim) {
                        var data = collectIncomplete();

                        if (data.unanswered.length || data.missingDrive.length) {
                            markIncomplete(data);
                            renderIncomplete(data);
                            if (window.App && App.toast) {
                                App.toast({
                                    title: 'Isian belum lengkap',
                                    description: 'Lengkapi seluruh butir dan tautan bukti dukung sebelum mengirim.',
                                    tone: 'warning'
                                });
                            }
                            if (incompleteModal && incompleteModal.showModal) incompleteModal.showModal();
                            return;
                        }

                        var ask = function () {
                            window.App.confirm({
                                title: 'Kirim Formulir F-01?',
                                description: 'Pastikan seluruh isian dan tautan bukti dukung sudah benar. Setelah dikirim, formulir tidak dapat diisi atau diperbarui lagi.',
                                confirm: 'Ya, kirim sekarang'
                            }).then(function (ok) {
                                if (ok) beginSubmit(submitter, 'submit');
                            });
                        };
                        if (window.App && window.App.confirm) { ask(); } else { setTimeout(ask, 60); }
                        return;
                    }

                    beginSubmit(submitter, 'draft');
                });

                document.querySelectorAll('[id^="aspek-"]').forEach(function (sec) {
                    sec.addEventListener('click', function () {
                        var url = new URL(window.location.href);
                        url.searchParams.set('aspek', sec.id.replace('aspek-', ''));
                        window.history.replaceState({}, '', url);
                    });
                });
            })();
        </script>
    @endif
@endpush
