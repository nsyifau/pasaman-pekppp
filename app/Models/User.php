<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'opd_id',
        'phone',
        'nip',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Daftar peran yang tersedia di aplikasi.
     *
     * Role `evaluator` telah dilebur ke `admin`: admin juga bertindak
     * sebagai penilai (F-02 & F-03).
     */
    public static function roles(): array
    {
        return [
            'admin' => 'Admin Utama / Penilai',
            'opd' => 'User Unit Layanan (OPD/Puskesmas/Kecamatan/Nagari)',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isEvaluator(): bool
    {
        return $this->isAdmin();
    }

    public function isOpd(): bool
    {
        return $this->role === 'opd';
    }

    public function opd()
    {
        return $this->belongsTo(Opd::class);
    }
}
