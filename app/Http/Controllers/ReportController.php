<?php

namespace App\Http\Controllers;

use App\Models\Aspect;
use App\Models\Evaluation;
use App\Models\F01Answer;
use App\Models\F02Score;
use App\Models\Opd;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Halaman pemilih unit untuk mencetak dokumen laporan.
     */
    public function index(): View
    {
        $year = (string) request('year', '');

        $query = Evaluation::with('opd')->orderByDesc('year')->orderBy('opd_id');

        if ($year !== '' && is_numeric($year)) {
            $query->where('year', (int) $year);
        }

        return view('laporan.index', [
            'evaluations' => $query->get(),
            'availableYears' => $this->availableYears(),
            'year' => $year,
            'categories' => Opd::categories(),
        ]);
    }

    /**
     * Berita Acara hasil penilaian per unit layanan (siap cetak).
     */
    public function beritaAcara(Evaluation $evaluation): View
    {
        $evaluation->load('opd');

        return view('laporan.berita-acara', [
            'evaluation' => $evaluation,
            'recap' => $evaluation->f02RecapByAspect(),
            'baNomor' => $evaluation->ba_nomor ?: $this->defaultNomorBa($evaluation),
            'baTempat' => $evaluation->ba_tempat ?: config('pekppp.ttd.tempat', 'Lubuk Sikaping'),
            'baTanggal' => $evaluation->ba_tanggal ?? $evaluation->evaluated_at ?? now(),
        ]);
    }

    /**
     * Simpan identitas Berita Acara & penanda tangan pada sesi evaluasi.
     */
    public function simpanTandaTangan(Request $request, Evaluation $evaluation): RedirectResponse
    {
        $data = $request->validate([
            'ba_nomor' => ['nullable', 'string', 'max:150'],
            'ba_tempat' => ['nullable', 'string', 'max:100'],
            'ba_tanggal' => ['nullable', 'date'],
            'pejabat_nama' => ['nullable', 'string', 'max:150'],
            'pejabat_jabatan' => ['nullable', 'string', 'max:150'],
            'pejabat_nip' => ['nullable', 'string', 'max:50'],
            'penilai_nama' => ['nullable', 'string', 'max:150'],
            'penilai_jabatan' => ['nullable', 'string', 'max:150'],
            'penilai_nip' => ['nullable', 'string', 'max:50'],
        ], [
            'ba_tanggal.date' => 'Tanggal Berita Acara tidak valid.',
        ]);

        $evaluation->fill($data)->save();

        return redirect()
            ->route('laporan.berita_acara', $evaluation)
            ->with('success', 'Identitas Berita Acara dan penanda tangan berhasil disimpan.');
    }

    /**
     * Rekapitulasi skor F-02 per aspek, indikator, dan butir (siap cetak).
     */
    public function rekapitulasi(Evaluation $evaluation): View
    {
        $evaluation->load('opd');

        $scores = F02Score::with('question')
            ->where('evaluation_id', $evaluation->id)
            ->get()
            ->keyBy('f01_question_id');

        $answers = F01Answer::where('evaluation_id', $evaluation->id)
            ->get()
            ->keyBy('f01_question_id');

        $recap = $evaluation->f02RecapByAspect();

        return view('laporan.rekapitulasi', [
            'evaluation' => $evaluation,
            'recap' => $recap,
            'scores' => $scores,
            'answers' => $answers,
            'baNomor' => $evaluation->ba_nomor ?: $this->defaultNomorBa($evaluation),
            'baTempat' => $evaluation->ba_tempat ?: config('pekppp.ttd.tempat', 'Lubuk Sikaping'),
            'baTanggal' => $evaluation->ba_tanggal ?? $evaluation->evaluated_at ?? now(),
        ]);
    }

    /**
     * Nomor Berita Acara usulan bila belum diisi admin.
     */
    private function defaultNomorBa(Evaluation $evaluation): string
    {
        $slug = Str::slug($evaluation->opd?->name ?? 'unit-layanan');

        return 'BA/PEKPPP/'.$evaluation->year.'/'.$slug;
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
