<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Halaman portofolio publik, datanya dibaca dari database akun pemilik portofolio.
Route::controller(PageController::class)->group(function () {
    Route::get('/', 'beranda')->name('beranda');
    Route::get('/beranda', 'beranda');
    Route::get('/profil-mahasiswa', 'profil')->name('profil');
    Route::get('/ide-agent', 'ideAgent')->name('ide-agent');

    // Menyimpan proposal ke database, jadi wajib login.
    Route::post('/ide-agent', 'submitIde')
        ->middleware(['auth', 'verified'])
        ->name('ide-agent.submit');
});

// Dashboard profil: hanya untuk user yang login dan sudah terverifikasi.
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
