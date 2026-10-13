<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\EvaluationPeriod;
use App\Models\EvaluationPeriodLog;
use App\Models\Opd;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class EvaluationPeriodController extends Controller
{
    /**
     * Daftar periode penilaian beserta statusnya.
     */
    public function index(): View
    {
        $periods = EvaluationPeriod::with(['logs.user', 'openedBy', 'closedBy', 'lockedBy'])
            ->orderByDesc('year')
            ->get();

        $activeCount = Opd::where('is_active', true)->count();

        $stats = $periods->mapWithKeys(function (EvaluationPeriod $period) use ($activeCount) {
            $query = Evaluation::where('year', $period->year);

            return [$period->id => [
                'units' => (clone $query)->count(),
                'scored' => (clone $query)->whereIn('status', ['in_review', 'verified', 'completed'])->count(),
                'active_opd' => $activeCount,
            ]];
        });

        return view('evaluations.periods', [
            'periods' => $periods,
            'stats' => $stats,
            'activeOpdCount' => $activeCount,
            'currentPeriod' => EvaluationPeriod::current(),
            'statuses' => EvaluationPeriod::statuses(),
            'logActions' => EvaluationPeriodLog::actions(),
        ]);
    }

    /**
     * Buka periode baru: buat periode + sesi evaluasi draft untuk semua unit aktif.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'year.required' => 'Tahun periode wajib diisi.',
            'year.integer' => 'Tahun periode harus berupa angka.',
            'year.min' => 'Tahun periode minimal 2000.',
            'year.max' => 'Tahun periode maksimal 2100.',
        ]);

        $year = (int) $data['year'];

        $existing = EvaluationPeriod::where('year', $year)->first();

        if ($existing && $existing->isOpen()) {
            return back()->with('error', 'Periode '.$year.' sudah dalam keadaan dibuka.');
        }

        if ($existing && $existing->isLocked()) {
            return back()->with('error', 'Periode '.$year.' sudah dikunci dan tidak dapat dibuka kembali.');
        }

        $open = EvaluationPeriod::current();

        if ($open && $open->year !== $year) {
            return back()->with('error', 'Periode '.$open->year.' masih dibuka. Tutup atau kunci periode tersebut sebelum membuka periode baru.');
        }

        $created = DB::transaction(function () use ($year, $data, $existing) {
            $period = $existing ?: new EvaluationPeriod(['year' => $year]);
            $period->fill([
                'status' => 'open',
                'notes' => $data['notes'] ?? $period->notes,
                'opened_at' => now(),
                'closed_at' => null,
                'locked_at' => null,
                'opened_by' => auth()->id(),
                'closed_by' => null,
                'locked_by' => null,
            ]);
            $period->save();

            $generated = 0;

            foreach (Opd::where('is_active', true)->orderBy('name')->get() as $opd) {
                $evaluation = Evaluation::firstOrCreate(
                    ['opd_id' => $opd->id, 'year' => $year],
                    ['status' => 'draft'],
                );

                if ($evaluation->wasRecentlyCreated) {
                    $generated++;
                }
            }

            EvaluationPeriodLog::create([
                'evaluation_period_id' => $period->id,
                'action' => 'open',
                'reason' => $data['notes'] ?? null,
                'user_id' => auth()->id(),
            ]);

            return [$period, $generated];
        });

        [$period, $generated] = $created;

        return redirect()
            ->route('periode.index')
            ->with('success', 'Periode '.$period->year.' dibuka. '.$generated.' sesi evaluasi unit baru dibuat.');
    }

    /**
     * Tutup periode: unit tidak dapat lagi mengisi F-01, admin masih dapat menilai.
     */
    public function close(Request $request, EvaluationPeriod $period): RedirectResponse
    {
        if ($period->isLocked()) {
            return back()->with('error', 'Periode yang sudah dikunci tidak dapat ditutup lagi.');
        }

        if ($period->isClosed()) {
            return back()->with('error', 'Periode '.$period->year.' sudah ditutup.');
        }

        $data = $request->validate(['reason' => ['nullable', 'string', 'max:500']]);

        $period->update([
            'status' => 'closed',
            'closed_at' => now(),
            'closed_by' => auth()->id(),
        ]);

        EvaluationPeriodLog::create([
            'evaluation_period_id' => $period->id,
            'action' => 'close',
            'reason' => $data['reason'] ?? null,
            'user_id' => auth()->id(),
        ]);

        return back()->with('success', 'Periode '.$period->year.' ditutup. Unit layanan tidak dapat mengubah isian F-01.');
    }

    /**
     * Kunci periode: seluruh perubahan menjadi read-only (arsip final).
     */
    public function lock(Request $request, EvaluationPeriod $period): RedirectResponse
    {
        if ($period->isLocked()) {
            return back()->with('error', 'Periode '.$period->year.' sudah dikunci.');
        }

        $data = $request->validate(['reason' => ['nullable', 'string', 'max:500']]);

        $period->update([
            'status' => 'locked',
            'closed_at' => $period->closed_at ?? now(),
            'locked_at' => now(),
            'closed_by' => $period->closed_by ?? auth()->id(),
            'locked_by' => auth()->id(),
        ]);

        EvaluationPeriodLog::create([
            'evaluation_period_id' => $period->id,
            'action' => 'lock',
            'reason' => $data['reason'] ?? null,
            'user_id' => auth()->id(),
        ]);

        return back()->with('success', 'Periode '.$period->year.' dikunci. Seluruh data tahun ini bersifat read-only.');
    }

    /**
     * Buka kembali periode yang ditutup (bukan yang dikunci), wajib dengan alasan.
     */
    public function reopen(Request $request, EvaluationPeriod $period): RedirectResponse
    {
        if ($period->isLocked()) {
            return back()->with('error', 'Periode yang sudah dikunci tidak dapat dibuka kembali.');
        }

        if ($period->isOpen()) {
            return back()->with('error', 'Periode '.$period->year.' sudah dalam keadaan dibuka.');
        }

        $open = EvaluationPeriod::current();

        if ($open && $open->year !== $period->year) {
            return back()->with('error', 'Periode '.$open->year.' masih dibuka. Hanya satu periode dapat dibuka pada satu waktu.');
        }

        $data = $request->validate([
            'reason' => ['required', 'string', 'min:5', 'max:500'],
        ], [
            'reason.required' => 'Alasan pembukaan kembali periode wajib diisi.',
            'reason.min' => 'Alasan minimal 5 karakter.',
        ]);

        $period->update([
            'status' => 'open',
            'opened_at' => now(),
            'closed_at' => null,
            'closed_by' => null,
            'opened_by' => auth()->id(),
        ]);

        EvaluationPeriodLog::create([
            'evaluation_period_id' => $period->id,
            'action' => 'reopen',
            'reason' => $data['reason'],
            'user_id' => auth()->id(),
        ]);

        return back()->with('success', 'Periode '.$period->year.' dibuka kembali.');
    }
}
