<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aspect extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'weight',
        'order',
    ];

    public function indicators()
    {
        return $this->hasMany(Indicator::class)->orderBy('number');
    }
}
