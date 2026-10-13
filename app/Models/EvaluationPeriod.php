<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EvaluationPeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'status',
        'notes',
        'opened_at',
        'closed_at',
        'locked_at',
        'opened_by',
        'closed_by',
        'locked_by',
    ];

    protected $casts = [
        'year' => 'integer',
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
        'locked_at' => 'datetime',
    ];

    public function logs(): HasMany
    {
        return $this->hasMany(EvaluationPeriodLog::class);
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class, 'year', 'year');
    }

    public function openedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'opened_by');
    }

    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function lockedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    /**
     * Periode yang sedang dibuka (hanya satu pada satu waktu).
     */
    public static function current(): ?self
    {
        return static::where('status', 'open')->orderByDesc('year')->first();
    }

    /**
     * Tahun aktif yang dipakai sebagai default di seluruh halaman.
     */
    public static function activeYear(): int
    {
        return static::current()?->year
            ?? (int) (static::orderByDesc('year')->value('year') ?? date('Y'));
    }

    /**
     * Daftar tahun periode terurut menurun.
     *
     * @return array<int, int>
     */
    public static function years(): array
    {
        $years = static::query()->orderByDesc('year')->pluck('year')
            ->map(fn ($y) => (int) $y)
            ->all();

        $years[] = (int) date('Y');

        $years = array_values(array_unique($years));
        rsort($years);

        return $years;
    }

    /**
     * @return array<string, string>
     */
    public static function statuses(): array
    {
        return [
            'open' => 'Dibuka',
            'closed' => 'Ditutup',
            'locked' => 'Dikunci',
        ];
    }

    public static function statusTone(?string $status): string
    {
        return match ($status) {
            'open' => 'success',
            'closed' => 'warning',
            'locked' => 'danger',
            default => 'neutral',
        };
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    public function isLocked(): bool
    {
        return $this->status === 'locked';
    }
}
