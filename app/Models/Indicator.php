<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Indicator extends Model
{
    use HasFactory;

    protected $fillable = [
        'aspect_id',
        'code',
        'name',
        'description',
        'number',
        'rubric_scales',
    ];

    protected $casts = [
        'rubric_scales' => 'array',
    ];

    public function aspect()
    {
        return $this->belongsTo(Aspect::class);
    }

    public function f01Questions()
    {
        return $this->hasMany(F01Question::class)->orderBy('order');
    }

    public function f02Scores()
    {
        return $this->hasMany(F02Score::class);
    }
}
