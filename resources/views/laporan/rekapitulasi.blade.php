@php
    $statuses = \App\Models\Evaluation::statuses();
    $categories = \App\Models\Opd::categories();
    $weight = fn ($w) => rtrim(rtrim(number_format((float) $w, 2), '0'), ',');
    $angka = fn ($v) => number_format((float) $v, 3, ',', '.');

    $unit = $evaluation->opd?->name ?? '—';
    $kategori = $categories[$evaluation->opd?->category] ?? $evaluation->opd?->category ?? '—';
    $totalButir = array_sum(array_column($recap, 'questions'));
    $totalDinilai = array_sum(array_column($recap, 'scored'));
    $predikat = $evaluation->category_ipp
        ? ($evaluation->category_ipp.' — '.($evaluation->category_ipp_label ?? ''))
        : 'Belum ditetapkan';

    $answerText = function ($questionId) use ($answers) {
        $answer = $answers->get($questionId)?->answer_value;

        if (is_array($answer)) {
            return implode(', ', array_map('strval', $answer));
        }

        return trim((string) ($answer ?? ''));
    };
@endphp

<x-layouts.document title="Rekapitulasi - {{ $unit }}">
    <x-slot:toolbar>
        <span class="doc-toolbar-title">{{ $evaluation->opd?->name }} · {{ $evaluation->year }}</span>
    </x-slot:toolbar>

    <div class="title-section">
        <h2>Rekapitulasi Hasil Penilaian Kinerja Pelayanan Publik</h2>
        <p>Formulir F-02 Per Aspek, Indikator, dan Butir Pertanyaan — Nomor: {{ $baNomor }}</p>
    </div>

    <table class="info-table">
        <tr>
            <th>Unit Layanan / Lokus</th>
            <td>{{ $unit }}</td>
            <th>Kategori Unit</th>
            <td>{{ $kategori }}</td>
        </tr>
        <tr>
            <th>Tahun Penilaian</th>
            <td>{{ $evaluation->year }}</td>
            <th>Status Penilaian</th>
            <td>{{ $statuses[$evaluation->status] ?? $evaluation->status }}</td>
        </tr>
        <tr>
            <th>Butir Dinilai</th>
            <td>{{ $totalDinilai }} dari {{ $totalButir }} butir</td>
            <th>Penilai</th>
            <td>{{ $evaluation->penilai_nama ?: ($evaluation->evaluator?->name ?? '—') }}</td>
        </tr>
        <tr>
            <th>Skor F-02</th>
            <td>{{ $angka($evaluation->score_f02) }} (75%)</td>
            <th>Skor F-03</th>
            <td>{{ $angka($evaluation->score_f03) }} (25%)</td>
        </tr>
        <tr>
            <th>Indeks Pelayanan Publik (IPP)</th>
            <td>{{ $angka($evaluation->final_score_ipp) }}</td>
            <th>Predikat</th>
            <td>{{ $predikat }}</td>
        </tr>
    </table>

    @foreach ($recap as $i => $row)
        <p class="section-title">Aspek {{ $i + 1 }} — {{ $row['aspect']->name }} (Bobot {{ $weight($row['aspect']->weight) }}%)</p>

        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:34px;">No</th>
                    <th style="width:60px;">Kode</th>
                    <th>Butir Pertanyaan</th>
                    <th style="width:150px;">Jawaban F-01</th>
                    <th style="width:48px;">Skor</th>
                    <th style="width:150px;">Catatan Penilai</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($row['indicators'] as $indicatorRow)
                    <tr class="aspek-row">
                        <td class="num">{{ $indicatorRow['indicator']->number }}</td>
                        <td class="num">{{ $indicatorRow['indicator']->code }}</td>
                        <td colspan="3">Indikator {{ $indicatorRow['indicator']->number }} — {{ $indicatorRow['indicator']->name }}</td>
                        <td class="num">Rata-rata: {{ $angka($indicatorRow['average']) }} ({{ $indicatorRow['scored'] }}/{{ $indicatorRow['questions'] }})</td>
                    </tr>

                    @foreach ($indicatorRow['indicator']->f01Questions as $q)
                        @php
                            $score = $scores->get($q->id);
                        @endphp
                        <tr>
                            <td class="num">{{ $loop->iteration }}</td>
                            <td class="num">{{ $q->question_code }}</td>
                            <td>{{ $q->question_text }}</td>
                            <td>{{ $answerText($q->id) !== '' ? $answerText($q->id) : '—' }}</td>
                            <td class="num">{{ $score ? $score->score : '—' }}</td>
                            <td>{{ $score?->notes ?: '—' }}</td>
                        </tr>
                    @endforeach
                @endforeach

                <tr class="subtotal">
                    <td colspan="4" class="right">Rata-rata Aspek — {{ $row['aspect']->name }}</td>
                    <td class="num">{{ $angka($row['average']) }}</td>
                    <td class="num">Nilai terbobot: {{ $angka($row['weighted']) }}</td>
                </tr>
            </tbody>
        </table>
    @endforeach

    <table class="sign-block">
        <tr>
            <td>Penilai / Tim Evaluator</td>
            <td>
                {{ $baTempat }}, {{ $baTanggal->translatedFormat('d F Y') }}<br>
                Pejabat Berwenang
            </td>
        </tr>
        <tr>
            <td><div class="ttd-space"></div><p class="ttd-name">{{ $evaluation->penilai_nama ?: '..............................' }}</p><p class="ttd-nip">NIP. {{ $evaluation->penilai_nip ?: '..............................' }}</p></td>
            <td><div class="ttd-space"></div><p class="ttd-name">{{ $evaluation->pejabat_nama ?: '..............................' }}</p><p class="ttd-nip">NIP. {{ $evaluation->pejabat_nip ?: '..............................' }}</p><p class="muted" style="margin:0;">{{ $evaluation->pejabat_jabatan ?: '' }}</p></td>
        </tr>
    </table>
</x-layouts.document>
