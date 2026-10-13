<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\EvaluationPeriod;
use App\Models\Opd;
use Illuminate\View\View;

class LeaderboardController extends Controller
{
    /**
     * Papan peringkat hasil penilaian PEKPPP Mandiri.
     */
    public function index(): View
    {
        $selectedYear = (int) request('tahun', EvaluationPeriod::activeYear());
        $activeCategory = (string) request('kategori', 'all');
        $search = trim((string) request('q', ''));

        $validCategories = array_keys(Opd::categories());
        if (! in_array($activeCategory, $validCategories, true)) {
            $activeCategory = 'all';
        }

        $query = Evaluation::with('opd')->where('year', $selectedYear);

        if ($activeCategory !== 'all') {
            $query->whereHas('opd', fn ($q) => $q->where('category', $activeCategory));
        }

        if ($search !== '') {
            $query->whereHas('opd', fn ($q) => $q->where('name', 'like', "%{$search}%"));
        }

        $rankings = $query->orderByDesc('final_score_ipp')->get();

        // Statistik selalu dihitung dari seluruh unit pada tahun terpilih.
        $all = Evaluation::with('opd')->where('year', $selectedYear)->get();

        $categoryStats = [
            'total_units' => $all->count(),
            'avg_ipp' => round((float) ($all->avg('final_score_ipp') ?? 0), 3),
            'top_unit' => $all->sortByDesc('final_score_ipp')->first(),
            'prima_count' => $all->where('category_ipp', 'A')->count(),
            'sangat_baik_count' => $all->where('category_ipp', 'A-')->count(),
            'baik_count' => $all->where('category_ipp', 'B')->count(),
        ];

        return view('peringkat.index', [
            'rankings' => $rankings,
            'categoryStats' => $categoryStats,
            'activeCategory' => $activeCategory,
            'selectedYear' => $selectedYear,
            'availableYears' => EvaluationPeriod::years(),
            'categories' => Opd::categories(),
        ]);
    }

    /**
     * @return array<int, int>
     */
    private function availableYears(): array
    {
        return EvaluationPeriod::years();
    }
}
