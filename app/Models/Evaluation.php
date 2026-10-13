<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'opd_id',
        'year',
        'status',
        'submitted_at',
        'evaluated_by',
        'evaluated_at',
        'score_f02',
        'score_f03_manual',
        'score_f03',
        'final_score_ipp',
        'category_ipp',
        'category_ipp_label',
        'evaluator_notes',
        'ba_nomor',
        'ba_tempat',
        'ba_tanggal',
        'pejabat_nama',
        'pejabat_jabatan',
        'pejabat_nip',
        'penilai_nama',
        'penilai_jabatan',
        'penilai_nip',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'evaluated_at' => 'datetime',
        'ba_tanggal' => 'date',
        'score_f02' => 'decimal:4',
        'score_f03_manual' => 'decimal:2',
        'score_f03' => 'decimal:4',
        'final_score_ipp' => 'decimal:4',
    ];

    public function opd()
    {
        return $this->belongsTo(Opd::class);
    }

    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluated_by');
    }

    public function f01Answers()
    {
        return $this->hasMany(F01Answer::class);
    }

    public function f02Scores()
    {
        return $this->hasMany(F02Score::class);
    }

    /**
     * Hitung total skor F02, F03, IPP dan kategori sesuai regulasi Pasaman
     */
    public function recalculateScores(): void
    {
        // 1. Skor F-02 dihitung berjenjang dari skor per butir pertanyaan F-01:
        //    skor butir -> rata-rata skor indikator -> rata-rata skor aspek -> terbobot per aspek.
        $scoresByQuestion = F02Score::where('evaluation_id', $this->id)
            ->pluck('score', 'f01_question_id');

        $aspects = Aspect::with(['indicators.f01Questions'])->where('weight', '>', 0)->get();
        $totalF02Score = 0;

        foreach ($aspects as $aspect) {
            $aspectQuestionIds = $aspect->indicators
                ->flatMap(fn ($indicator) => $indicator->f01Questions->pluck('id'));

            if ($aspectQuestionIds->isEmpty()) {
                continue;
            }

            $avgAspectScore = $scoresByQuestion->only($aspectQuestionIds)->avg() ?? 0;

            // Bobot aspek (misal 24% = 0.24)
            $totalF02Score += ($avgAspectScore * ($aspect->weight / 100));
        }

        $this->score_f02 = round($totalF02Score, 4);

        // 2. Konversi nilai F-03 manual per unit (skala 1-100) ke skala 0-5
        $this->score_f03 = round(((float) $this->score_f03_manual) / 20, 4);

        // 3. Hitung IPP Final: (F02 * 75%) + (F03 * 25%)
        $finalIpp = ($this->score_f02 * 0.75) + ($this->score_f03 * 0.25);
        $this->final_score_ipp = round($finalIpp, 4);

        // 4. Tentukan Kategori IPP sesuai Tabel SK Bupati Pasaman
        [$category, $label] = self::determineCategory($this->final_score_ipp);
        $this->category_ipp = $category;
        $this->category_ipp_label = $label;

        $this->save();
    }

    /**
     * Rekap skor F-02 berjenjang per aspek (dan indikator di dalamnya).
     *
     * @return array<int, array{
     *     aspect: \App\Models\Aspect,
     *     questions: int,
     *     scored: int,
     *     average: float,
     *     weighted: float,
     *     indicators: array<int, array{indicator: \App\Models\Indicator, questions: int, scored: int, average: float}>
     * }>
     */
    public function f02RecapByAspect(): array
    {
        $scoresByQuestion = F02Score::where('evaluation_id', $this->id)
            ->pluck('score', 'f01_question_id');

        $aspects = Aspect::with(['indicators.f01Questions'])
            ->where('weight', '>', 0)
            ->orderBy('order')
            ->get();

        $recap = [];

        foreach ($aspects as $aspect) {
            $indicators = [];
            $questionCount = 0;
            $scoreSum = 0.0;
            $scored = 0;

            foreach ($aspect->indicators as $indicator) {
                $questionIds = $indicator->f01Questions->pluck('id');
                $scores = $scoresByQuestion->only($questionIds);

                $questionCount += $questionIds->count();
                $scoreSum += (float) $scores->sum();
                $scored += $scores->count();

                $indicators[] = [
                    'indicator' => $indicator,
                    'questions' => $questionIds->count(),
                    'scored' => $scores->count(),
                    'average' => $scores->count() > 0 ? round((float) $scores->avg(), 4) : 0.0,
                ];
            }

            $average = $scored > 0 ? round($scoreSum / $scored, 4) : 0.0;

            $recap[] = [
                'aspect' => $aspect,
                'questions' => $questionCount,
                'scored' => $scored,
                'average' => $average,
                'weighted' => round($average * ((float) $aspect->weight / 100), 4),
                'indicators' => $indicators,
            ];
        }

        return $recap;
    }

    public static function determineCategory(float $score): array
    {
        if ($score >= 4.51) return ['A', 'Pelayanan Prima'];
        if ($score >= 4.01) return ['A-', 'Sangat Baik'];
        if ($score >= 3.51) return ['B', 'Baik'];
        if ($score >= 3.01) return ['B-', 'Baik (Dengan Catatan)'];
        if ($score >= 2.51) return ['C', 'Cukup'];
        if ($score >= 2.01) return ['C-', 'Cukup (Dengan Catatan)'];
        if ($score >= 1.51) return ['D', 'Buruk'];
        if ($score >= 1.01) return ['E', 'Sangat Buruk'];
        return ['F', 'Gagal'];
    }

    /**
     * Daftar status evaluasi beserta label tampilannya.
     *
     * @return array<string, string>
     */
    public static function statuses(): array
    {
        return [
            'draft' => 'Draft Mandiri',
            'submitted' => 'Diajukan Unit Layanan',
            'in_review' => 'Proses Penilaian',
            'verified' => 'Terverifikasi',
            'completed' => 'Selesai & Final',
        ];
    }

    /**
     * Tabel kategori Indeks Pelayanan Publik sesuai Keputusan Bupati Pasaman.
     *
     * @return array<string, array{range: string, label: string, tone: string}>
     */
    public static function categoryRanges(): array
    {
        return [
            'A' => ['range' => '4,51 - 5,00', 'label' => 'Pelayanan Prima', 'tone' => 'success'],
            'A-' => ['range' => '4,01 - 4,50', 'label' => 'Sangat Baik', 'tone' => 'success'],
            'B' => ['range' => '3,51 - 4,00', 'label' => 'Baik', 'tone' => 'primary'],
            'B-' => ['range' => '3,01 - 3,50', 'label' => 'Baik (Dengan Catatan)', 'tone' => 'primary'],
            'C' => ['range' => '2,51 - 3,00', 'label' => 'Cukup', 'tone' => 'warning'],
            'C-' => ['range' => '2,01 - 2,50', 'label' => 'Cukup (Dengan Catatan)', 'tone' => 'warning'],
            'D' => ['range' => '1,51 - 2,00', 'label' => 'Buruk (Prioritas Pembinaan)', 'tone' => 'danger'],
            'E' => ['range' => '1,01 - 1,50', 'label' => 'Sangat Buruk', 'tone' => 'danger'],
            'F' => ['range' => '0,00 - 1,00', 'label' => 'Gagal', 'tone' => 'danger'],
        ];
    }

    /**
     * Formulir F-01 unit layanan terkunci setelah dikirim ke penilai.
     */
    public function isLockedForOpd(): bool
    {
        return in_array($this->status, ['submitted', 'in_review', 'verified', 'completed'], true);
    }

    /**
     * Warna badge Bootstrap untuk status evaluasi.
     */
    public static function statusTone(?string $status): string
    {
        return match ($status) {
            'submitted' => 'info',
            'in_review' => 'warning',
            'verified' => 'primary',
            'completed' => 'success',
            default => 'secondary',
        };
    }

    /**
     * Warna badge Bootstrap untuk predikat IPP unit ini.
     */
    public function categoryTone(): string
    {
        return self::categoryRanges()[$this->category_ipp]['tone'] ?? 'secondary';
    }
}
