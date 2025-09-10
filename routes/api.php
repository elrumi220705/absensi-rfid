<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SiswaAuthController;
use App\Http\Controllers\Api\SiswaJadwalController;

Route::prefix('siswa')->group(function () {
    Route::post('/login', [SiswaAuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [SiswaAuthController::class, 'me']);
        Route::post('/logout', [SiswaAuthController::class, 'logout']);

        // ===== Profil & Password (pakai SiswaAuthController) =====
        Route::post('/profile', [SiswaAuthController::class, 'updateProfile']);   // ubah nama & foto
        Route::post('/password', [SiswaAuthController::class, 'changePassword']); // ganti password

        Route::get('/jadwal', [SiswaJadwalController::class, 'index']);
    });
});
