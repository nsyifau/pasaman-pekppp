<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\EvaluationPeriod;
use App\Models\F01Answer;
use App\Models\F01Question;
use App\Models\Opd;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Dashboard ringkasan penilaian PEKPPP sesuai peran pengguna.
     */
    public function index(): View
    {
        $user = auth()->user();
        $year = (int) request('tahun', EvaluationPeriod::activeYear());

        $query = Evaluation::with('opd')->where('year', $year);

        // Unit layanan hanya melihat lokusnya sendiri.
        if ($user->isOpd() && $user->opd_id) {
            $query->where('opd_id', $user->opd_id);
        }

        $evaluations = $query->get();

        $totalUnits = $evaluations->count();
        $avgIpp = round((float) ($evaluations->avg('final_score_ipp') ?? 0), 3);
        $primaCount = $evaluations->whereIn('category_ipp', ['A', 'A-'])->count();

        // Distribusi predikat untuk diagram
        $distribution = [];
        foreach (array_keys(Evaluation::categoryRanges()) as $category) {
            $distribution[$category] = $evaluations->where('category_ipp', $category)->count();
        }

        // Progres pengisian F-01 (butir terjawab terhadap total butir x jumlah unit)
        $totalQuestions = F01Question::count();
        $expectedAnswers = $totalQuestions * $totalUnits;
        $answeredCount = F01Answer::whereIn('evaluation_id', $evaluations->pluck('id'))
            ->whereNotNull('answer_value')
            ->count();
        $f01Progress = $expectedAnswers > 0
            ? (int) min(100, round(($answeredCount / $expectedAnswers) * 100))
            : 0;

        // Rekap status evaluasi
        $statusCounts = [];
        foreach (Evaluation::statuses() as $status => $label) {
            $statusCounts[$status] = $evaluations->where('status', $status)->count();
        }

        return view('dashboard', [
            'year' => $year,
            'availableYears' => EvaluationPeriod::years(),
            'evaluations' => $evaluations,
            'totalUnits' => $totalUnits,
            'totalOpd' => Opd::where('is_active', true)->count(),
            'avgIpp' => $avgIpp,
            'primaCount' => $primaCount,
            'distribution' => $distribution,
            'f01Progress' => $f01Progress,
            'answeredCount' => $answeredCount,
            'expectedAnswers' => $expectedAnswers,
            'statusCounts' => $statusCounts,
            'topRankings' => $evaluations->sortByDesc('final_score_ipp')->take(5)->values(),
            'myEvaluation' => ($user->isOpd() && $user->opd_id) ? $evaluations->first() : null,
        ]);
    }

    /**
     * Daftar tahun evaluasi yang tersedia (tahun berjalan selalu disertakan).
     *
     * @return array<int, int>
     */
    private function availableYears(): array
    {
        return EvaluationPeriod::years();
    }
}
