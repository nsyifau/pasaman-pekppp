<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class F01Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'indicator_id',
        'question_code',
        'question_text',
        'question_type',
        'options',
        'help_text',
        'evidence_hint',
        'requires_drive_link',
        'order',
    ];

    protected $casts = [
        'options' => 'array',
        'requires_drive_link' => 'boolean',
    ];

    public function indicator()
    {
        return $this->belongsTo(Indicator::class);
    }

    public function answers()
    {
        return $this->hasMany(F01Answer::class);
    }
}
