<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\F01Controller;
use App\Http\Controllers\F02Controller;
use App\Http\Controllers\F03Controller;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\OpdController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| RUTE PUBLIK (tanpa login)
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
})->name('home');

/*
|--------------------------------------------------------------------------
| RUTE AUTENTIKASI (login memakai username)
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| RUTE ADMIN / TERLOGIN
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Instrumen PEKPPP - F-01 Penilaian Mandiri (hanya Unit Layanan / OPD)
    Route::middleware('role:opd')->group(function () {
        Route::get('/f01', [F01Controller::class, 'index'])->name('f01.index');
        Route::post('/f01/simpan-draft', [F01Controller::class, 'saveDraft'])->name('f01.save_draft');
        Route::post('/f01/kirim', [F01Controller::class, 'submit'])->name('f01.submit');
    });

    // Instrumen PEKPPP - F-02 & F-03 (Admin / Penilai)
    Route::middleware('role:admin')->group(function () {
        Route::get('/f02', [F02Controller::class, 'index'])->name('f02.index');
        Route::post('/f02', [F02Controller::class, 'store'])->name('f02.store');

        Route::get('/f03', [F03Controller::class, 'index'])->name('f03.index');
        Route::post('/f03', [F03Controller::class, 'store'])->name('f03.store');
    });

    // Laporan & Hasil
    Route::get('/peringkat', [LeaderboardController::class, 'index'])->name('peringkat.index');

    // Laporan & Cetak dokumen (Berita Acara & Rekapitulasi) - Admin/Penilai
    Route::middleware('role:admin')->group(function () {
        Route::get('/laporan', [ReportController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/berita-acara/{evaluation}', [ReportController::class, 'beritaAcara'])->name('laporan.berita_acara');
        Route::post('/laporan/berita-acara/{evaluation}/tanda-tangan', [ReportController::class, 'simpanTandaTangan'])->name('laporan.berita_acara.simpan');
        Route::get('/laporan/rekapitulasi/{evaluation}', [ReportController::class, 'rekapitulasi'])->name('laporan.rekapitulasi');
    });

    // Master Data - Unit Layanan & Pengguna (khusus Admin)
    Route::middleware('role:admin')->group(function () {
        Route::resource('opds', OpdController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('users', UserController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::put('/users/{user}/reset-password', [UserController::class, 'resetPassword'])
            ->name('users.reset_password');
    });

    // Master Data - Evaluasi Unit (Admin)
    Route::middleware('role:admin')->group(function () {
        Route::resource('evaluations', EvaluationController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::post('/evaluations/{evaluation}/recalculate', [EvaluationController::class, 'recalculate'])
            ->name('evaluations.recalculate');
    });

    // Akun
    Route::get('/reset-password', [ResetPasswordController::class, 'showForm'])->name('reset-password.form');
    Route::put('/reset-password', [ResetPasswordController::class, 'reset'])->name('reset-password.update');
});
