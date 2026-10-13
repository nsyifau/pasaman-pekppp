<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Opd extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'is_active',
    ];

    public static function categories(): array
    {
        return [
            'opd' => 'Organisasi Perangkat Daerah (OPD)',
            'puskesmas' => 'Puskesmas',
            'kecamatan' => 'Kecamatan',
            'nagari' => 'Nagari',
        ];
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }
}
