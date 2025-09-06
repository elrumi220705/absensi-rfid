<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SiswaAuthController;

Route::prefix('siswa')->group(function () {
    Route::post('/login', [SiswaAuthController::class, 'login']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [SiswaAuthController::class, 'me']);
        Route::post('/logout', [SiswaAuthController::class, 'logout']);
    });
});
