<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class F02Score extends Model
{
    use HasFactory;

    protected $fillable = [
        'evaluation_id',
        'f01_question_id',
        'score',
        'notes',
    ];

    public function evaluation()
    {
        return $this->belongsTo(Evaluation::class);
    }

    public function question()
    {
        return $this->belongsTo(F01Question::class, 'f01_question_id');
    }
}
