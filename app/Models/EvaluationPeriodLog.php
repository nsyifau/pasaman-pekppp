<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluationPeriodLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'evaluation_period_id',
        'action',
        'reason',
        'user_id',
    ];

    public function period(): BelongsTo
    {
        return $this->belongsTo(EvaluationPeriod::class, 'evaluation_period_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return array<string, string>
     */
    public static function actions(): array
    {
        return [
            'open' => 'Periode Dibuka',
            'close' => 'Periode Ditutup',
            'lock' => 'Periode Dikunci',
            'reopen' => 'Periode Dibuka Kembali',
        ];
    }
}
