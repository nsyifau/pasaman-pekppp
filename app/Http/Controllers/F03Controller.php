<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class F03Controller extends Controller
{
    /**
     * Formulir F-03: input nilai persepsi per unit layanan (skala 1-100),
     * hanya untuk unit yang telah mengirim isian F-01.
     */
    public function index(): View
    {
        $evaluations = Evaluation::with('opd')
            ->whereIn('status', ['submitted', 'in_review', 'verified', 'completed'])
            ->orderByDesc('year')
            ->orderBy('opd_id')
            ->get();

        $selectedEvaluationId = (int) request('evaluasi', $evaluations->first()?->id ?? 0);
        $selectedEvaluation = $selectedEvaluationId > 0
            ? Evaluation::with('opd')->find($selectedEvaluationId)
            : null;

        return view('f03.index', [
            'evaluations' => $evaluations,
            'selectedEvaluationId' => $selectedEvaluation?->id,
            'selectedEvaluation' => $selectedEvaluation,
        ]);
    }

    /**
     * Simpan nilai F-03 per unit lalu hitung ulang Indeks Pelayanan Publik.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'evaluation_id' => ['required', 'integer', 'exists:evaluations,id'],
            'score_f03_manual' => ['required', 'numeric', 'min:1', 'max:100'],
        ], [
            'score_f03_manual.required' => 'Nilai F-03 wajib diisi.',
            'score_f03_manual.numeric' => 'Nilai F-03 harus berupa angka.',
            'score_f03_manual.min' => 'Nilai F-03 minimal 1.',
            'score_f03_manual.max' => 'Nilai F-03 maksimal 100.',
        ]);

        $evaluation = Evaluation::findOrFail($data['evaluation_id']);
        $evaluation->score_f03_manual = $data['score_f03_manual'];
        $evaluation->recalculateScores();

        return back()->with('success', 'Nilai F-03 unit "' . ($evaluation->opd?->name ?? '-') . '" berhasil disimpan. '
            . 'Nilai ' . number_format((float) $evaluation->score_f03_manual, 2) . ' / 100 (setara '
            . number_format((float) $evaluation->score_f03, 2) . ' / 5) — Indeks IPP terkini: '
            . number_format((float) $evaluation->final_score_ipp, 3) . ' (' . ($evaluation->category_ipp ?? '-') . ')');
    }
}
