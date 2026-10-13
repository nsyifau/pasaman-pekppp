<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\NormalizesAnswerInput;
use App\Models\Aspect;
use App\Models\Evaluation;
use App\Models\F01Answer;
use App\Models\F01Question;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class F01Controller extends Controller
{
    use NormalizesAnswerInput;

    /**
     * Formulir F-01: Penilaian Mandiri Pelayanan Publik.
     */
    public function index(): View
    {
        $evaluation = $this->resolveEvaluation();

        $answers = [];
        $driveLinks = [];
        $opdNotes = [];

        if ($evaluation) {
            foreach (F01Answer::where('evaluation_id', $evaluation->id)->get() as $answer) {
                $answers[$answer->f01_question_id] = $answer->answer_value;
                $driveLinks[$answer->f01_question_id] = $answer->drive_link;
                $opdNotes[$answer->f01_question_id] = $answer->opd_note;
            }
        }

        $aspects = Aspect::with(['indicators.f01Questions'])->orderBy('order')->get();
        $activeAspectCode = request('aspek') ?: $aspects->first()?->code;

        return view('f01.index', [
            'evaluation' => $evaluation,
            'locked' => $evaluation?->isLockedForOpd() ?? false,
            'answers' => $answers,
            'driveLinks' => $driveLinks,
            'opdNotes' => $opdNotes,
            'aspects' => $aspects,
            'activeAspectCode' => $activeAspectCode,
            'currentAspect' => $aspects->firstWhere('code', $activeAspectCode),
            'stats' => $this->completionStats($answers, $driveLinks),
        ]);
    }

    /**
     * Simpan seluruh isian F-01 sebagai draft.
     */
    public function saveDraft(Request $request): RedirectResponse
    {
        $evaluation = $this->resolveEvaluation();

        if (! $evaluation) {
            return back()->with('error', 'Unit evaluasi tidak ditemukan. Hubungi Administrator.');
        }

        if ($evaluation->isLockedForOpd()) {
            return back()->with('error', 'Formulir F-01 sudah dikirim ke penilai dan tidak dapat diubah.');
        }

        $this->persistAnswers($request, $evaluation);

        return back()->with('success', 'Draft isian F-01 dan tautan Google Drive bukti dukung berhasil disimpan.');
    }

    /**
     * Simpan lalu kirim formulir F-01 ke Tim Evaluator.
     */
    public function submit(Request $request): RedirectResponse
    {
        $evaluation = $this->resolveEvaluation();

        if (! $evaluation) {
            return back()->with('error', 'Unit evaluasi tidak ditemukan. Hubungi Administrator.');
        }

        if ($evaluation->isLockedForOpd()) {
            return back()->with('error', 'Formulir F-01 sudah dikirim ke penilai dan tidak dapat diubah.');
        }

        $this->persistAnswers($request, $evaluation);

        $evaluation->status = 'submitted';
        $evaluation->submitted_at = now();
        $evaluation->save();

        return redirect()
            ->route('f01.index')
            ->with('success', 'Formulir F-01 berhasil dikirim ke Tim Evaluator untuk diverifikasi dan dinilai.');
    }

    /**
     * Tentukan sesi evaluasi milik unit layanan pengguna yang sedang login.
     * F-01 hanya diisi oleh akun Unit Layanan (OPD).
     */
    private function resolveEvaluation(): ?Evaluation
    {
        $user = auth()->user();

        if (! $user->isOpd() || ! $user->opd_id) {
            return null;
        }

        return Evaluation::firstOrCreate(
            ['opd_id' => $user->opd_id, 'year' => (int) date('Y')],
            ['status' => 'draft'],
        );
    }

    /**
     * Simpan jawaban, tautan Google Drive, dan catatan unit layanan.
     */
    private function persistAnswers(Request $request, Evaluation $evaluation): void
    {
        $answers = (array) $request->input('answers', []);
        $driveLinks = (array) $request->input('drive_links', []);
        $opdNotes = (array) $request->input('opd_notes', []);

        $questionIds = array_unique(array_merge(
            array_keys($answers),
            array_keys($driveLinks),
            array_keys($opdNotes),
        ));

        foreach ($questionIds as $questionId) {
            F01Answer::updateOrCreate(
                [
                    'evaluation_id' => $evaluation->id,
                    'f01_question_id' => (int) $questionId,
                ],
                [
                    'answer_value' => $this->normalizeAnswer($answers[$questionId] ?? null),
                    'drive_link' => $this->normalizeText($driveLinks[$questionId] ?? null),
                    'opd_note' => $this->normalizeText($opdNotes[$questionId] ?? null),
                ],
            );
        }
    }

    /**
     * Statistik kelengkapan pengisian instrumen F-01.
     *
     * @param  array<int, mixed>  $answers
     * @param  array<int, string|null>  $driveLinks
     * @return array{total: int, answered: int, drive_count: int, percentage: int}
     */
    private function completionStats(array $answers, array $driveLinks): array
    {
        $total = F01Question::count();

        $answered = 0;
        foreach ($answers as $value) {
            if (! empty($value)) {
                $answered++;
            }
        }

        $driveCount = 0;
        foreach ($driveLinks as $link) {
            if (! empty($link)) {
                $driveCount++;
            }
        }

        $percentage = $total > 0 ? (int) min(100, round(($answered / $total) * 100)) : 0;

        return [
            'total' => $total,
            'answered' => $answered,
            'drive_count' => $driveCount,
            'percentage' => $percentage,
        ];
    }
}
