<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MateriController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\PesertaController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('peserta', PesertaController::class);
    Route::get('/peserta/export/pdf', [PesertaController::class, 'export'])->name('peserta.export');
    Route::resource('materi', MateriController::class);
    Route::resource('absensi', AbsensiController::class);
    Route::get('/absensi/scan/{materi}', [AbsensiController::class, 'scan'])->name('absensi.scan');
    Route::get('/absensi/export/{materi}', [AbsensiController::class, 'export'])->name('absensi.export');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
