<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\NormalizesAnswerInput;
use App\Models\Aspect;
use App\Models\Evaluation;
use App\Models\F01Answer;
use App\Models\F02Score;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class F02Controller extends Controller
{
    use NormalizesAnswerInput;

    /**
     * Formulir F-02: penilaian skor 0-5 untuk setiap butir pertanyaan F-01.
     */
    public function index(): View
    {
        $evaluations = Evaluation::with('opd')->orderByDesc('year')->orderBy('opd_id')->get();

        $selectedEvaluationId = (int) request('evaluasi', $evaluations->first()?->id ?? 0);
        $selectedEvaluation = $selectedEvaluationId > 0
            ? Evaluation::with('opd')->find($selectedEvaluationId)
            : null;

        $scores = [];
        $evaluatorNotes = [];

        if ($selectedEvaluation) {
            foreach (F02Score::where('evaluation_id', $selectedEvaluation->id)->get() as $score) {
                $scores[$score->f01_question_id] = $score->score;
                $evaluatorNotes[$score->f01_question_id] = $score->notes;
            }
        }

        return view('f02.index', [
            'evaluations' => $evaluations,
            'selectedEvaluationId' => $selectedEvaluation?->id,
            'selectedEvaluation' => $selectedEvaluation,
            'scores' => $scores,
            'evaluatorNotes' => $evaluatorNotes,
            'aspects' => Aspect::with(['indicators.f01Questions'])
                ->where('weight', '>', 0)
                ->orderBy('order')
                ->get(),
            'f01Answers' => $selectedEvaluation
                ? F01Answer::where('evaluation_id', $selectedEvaluation->id)->get()->keyBy('f01_question_id')
                : collect(),
        ]);
    }

    /**
     * Simpan skor 0-5 per butir pertanyaan F-01 beserta catatan penilai.
     *
     * Jawaban F-01 tidak dapat diubah di sini; penilai hanya menetapkan skor
     * dan catatan berdasarkan bukti dukung yang dilampirkan unit layanan.
     */
    public function store(Request $request): RedirectResponse
    {
        $evaluation = Evaluation::find($request->input('evaluation_id'));

        if (! $evaluation) {
            return back()->with('error', 'Unit evaluasi tidak ditemukan.');
        }

        // Simpan skor F-02 (skala 0-5) beserta catatan per butir pertanyaan
        $notes = (array) $request->input('notes', []);

        foreach ((array) $request->input('scores', []) as $questionId => $score) {
            F02Score::updateOrCreate(
                [
                    'evaluation_id' => $evaluation->id,
                    'f01_question_id' => (int) $questionId,
                ],
                [
                    'score' => (int) $score,
                    'notes' => $this->normalizeText($notes[$questionId] ?? null),
                ],
            );
        }

        $evaluation->evaluated_by = auth()->id();
        $evaluation->evaluated_at = now();
        $evaluation->status = 'in_review';
        $evaluation->recalculateScores();

        return back()->with('success', 'Skor F-02 (per butir pertanyaan) berhasil disimpan. Nilai F-02 terbobot: '
            . number_format((float) $evaluation->score_f02, 3) . ' / 5.00 — Indeks IPP: '
            . number_format((float) $evaluation->final_score_ipp, 3) . ' (' . ($evaluation->category_ipp ?? '-') . ')');
    }
}
