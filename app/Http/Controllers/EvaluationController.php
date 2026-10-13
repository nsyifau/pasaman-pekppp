<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\Opd;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EvaluationController extends Controller
{
    /**
     * Daftar sesi evaluasi unit beserta form tambah/ubah (modal).
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $year = (string) $request->query('year', '');
        $status = (string) $request->query('status', '');

        $query = Evaluation::query()->with(['opd', 'evaluator']);

        if ($search !== '') {
            $query->whereHas('opd', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($year !== '' && is_numeric($year)) {
            $query->where('year', (int) $year);
        }

        if (array_key_exists($status, Evaluation::statuses())) {
            $query->where('status', $status);
        }

        return view('evaluations.index', [
            'evaluations' => $query->orderByDesc('year')->orderBy('opd_id')->paginate(10)->withQueryString(),
            'opds' => Opd::where('is_active', true)->orderBy('name')->get(),
            'statuses' => Evaluation::statuses(),
            'availableYears' => $this->availableYears(),
            'search' => $search,
            'year' => $year,
            'status' => $status,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $existing = Evaluation::where('opd_id', $data['opd_id'])->where('year', $data['year'])->first();

        if ($existing) {
            return redirect()
                ->route('evaluations.index')
                ->with('error', 'Sesi evaluasi untuk unit tersebut pada tahun ' . $data['year'] . ' sudah ada.');
        }

        $evaluation = Evaluation::create($data);

        return redirect()
            ->route('evaluations.index')
            ->with('success', 'Sesi evaluasi "' . $evaluation->opd?->name . '" tahun ' . $evaluation->year . ' berhasil dibuat.');
    }

    public function update(Request $request, Evaluation $evaluation): RedirectResponse
    {
        $data = $this->validated($request, $evaluation);

        $evaluation->update($data);

        return redirect()
            ->route('evaluations.index')
            ->with('success', 'Data evaluasi "' . ($evaluation->opd?->name ?? '-') . '" berhasil diperbarui.');
    }

    public function destroy(Evaluation $evaluation): RedirectResponse
    {
        $name = $evaluation->opd?->name ?? '-';
        $year = $evaluation->year;

        $evaluation->delete();

        return redirect()
            ->route('evaluations.index')
            ->with('success', 'Sesi evaluasi "' . $name . '" tahun ' . $year . ' berhasil dihapus.');
    }

    /**
     * Hitung ulang skor F-02, F-03, Indeks IPP, dan predikat.
     */
    public function recalculate(Evaluation $evaluation): RedirectResponse
    {
        $evaluation->recalculateScores();

        return redirect()
            ->route('evaluations.index')
            ->with('success', 'Skor "' . ($evaluation->opd?->name ?? '-') . '" berhasil dihitung ulang. Indeks IPP: '
                . number_format((float) $evaluation->final_score_ipp, 3) . ' (' . ($evaluation->category_ipp ?? '-') . ')');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?Evaluation $evaluation = null): array
    {
        return $request->validate([
            'opd_id' => ['required', Rule::exists('opds', 'id')],
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'status' => ['required', Rule::in(array_keys(Evaluation::statuses()))],
            'evaluator_notes' => ['nullable', 'string'],
        ], [
            'opd_id.required' => 'Unit layanan wajib dipilih.',
            'opd_id.exists' => 'Unit layanan tidak ditemukan.',
            'year.required' => 'Tahun evaluasi wajib diisi.',
            'year.integer' => 'Tahun evaluasi harus berupa angka.',
            'status.required' => 'Status evaluasi wajib dipilih.',
            'status.in' => 'Status evaluasi tidak dikenal.',
        ]);
    }

    /**
     * @return array<int, int>
     */
    private function availableYears(): array
    {
        $years = Evaluation::query()->distinct()->orderByDesc('year')->pluck('year')
            ->map(fn ($y) => (int) $y)
            ->all();

        $years[] = (int) date('Y');

        $years = array_values(array_unique($years));
        rsort($years);

        return $years;
    }
}
