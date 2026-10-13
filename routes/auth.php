<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rute Autentikasi (Breeze - dimodifikasi memakai username)
|--------------------------------------------------------------------------
| Registrasi, verifikasi email, dan reset password via email sengaja
| TIDAK disediakan. Pengguna dibuat oleh Admin melalui menu Master Data,
| dan penggantian sandi dilakukan lewat menu Akun > Ganti Password.
*/

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:6,1');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
