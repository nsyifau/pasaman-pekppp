@php
    /**
     * Panel satu aspek F-02.
     *
     * @var \App\Models\Aspect $asp
     * Scope yang dipakai dari view induk: $scores, $evaluatorNotes, $f01Answers, $weight.
     */
    $aspQuestions = $asp->indicators->flatMap->f01Questions;
    $aspTotal = $aspQuestions->count();
    $aspScored = $aspQuestions->filter(fn ($q) => isset($scores[$q->id]))->count();
@endphp

<div class="card">
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
                        <p class="mb-3 flex items-center gap-2 text-xs font-bold uppercase tracking-wide text-info">
                            <x-ui.icon name="file-text" class="h-4 w-4" /> Panduan Rubrik Penilaian (Keputusan Bupati Pasaman)
                        </p>
                        <div class="overflow-hidden rounded-lg border border-info/25 bg-card">
                            <table class="w-full border-collapse text-sm">
                                <thead>
                                    <tr class="border-b border-info/25 bg-info-soft/50 text-left">
                                        <th scope="col" class="w-16 px-3 py-2 text-center font-semibold">Skala</th>
                                        <th scope="col" class="px-3 py-2 font-semibold">Kriteria Penilaian</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-border">
                                    @foreach ($ind->rubric_scales as $scale => $desc)
                                        <tr @class([
                                            'align-top',
                                            'bg-success-soft/40' => (int) $scale === 5,
                                            'bg-danger-soft/40' => (int) $scale === 0,
                                        ])>
                                            <td class="px-3 py-2.5 text-center">
                                                <x-ui.badge :variant="match ((int) $scale) {
                                                    5 => 'success',
                                                    0 => 'danger',
                                                    default => 'outline',
                                                }" class="tabular-nums">{{ $scale }}</x-ui.badge>
                                            </td>
                                            <td class="px-3 py-2.5 leading-relaxed">{{ $desc }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
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
