<?php

namespace App\Http\Controllers\Concerns;

/**
 * Normalisasi input formulir instrumen PEKPPP sebelum disimpan.
 *
 * Jawaban F-01 dapat berupa string (radio/number/text) maupun array
 * (checkbox), sementara kolom `answer_value` bertipe JSON nullable.
 */
trait NormalizesAnswerInput
{
    /**
     * Ubah string kosong menjadi null dan buang anggota array yang kosong.
     */
    protected function normalizeAnswer(mixed $value): mixed
    {
        if (is_array($value)) {
            $value = array_values(array_filter(
                $value,
                fn ($item) => $item !== null && trim((string) $item) !== '',
            ));

            return $value === [] ? null : $value;
        }

        return $this->normalizeText($value);
    }

    /**
     * Ubah nilai teks kosong menjadi null.
     */
    protected function normalizeText(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
