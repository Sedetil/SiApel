<?php

use App\Http\Controllers\Api\AttendanceApiController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — SIABSEN POLINELA RFID
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    // Autentikasi Publik
    Route::post('/auth/login', [AuthController::class, 'login']);

    // Endpoint Terproteksi Token (Sanctum)
    Route::middleware(['auth:sanctum', 'role:admin,petugas'])->group(function () {
        // Info User & Logout
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        // Operasional Apel & RFID
        Route::get('/schedules/active', [AttendanceApiController::class, 'activeSchedules']);
        Route::get('/schedules/{id}/summary', [AttendanceApiController::class, 'scheduleSummary']);
        Route::post('/attendance/scan', [AttendanceApiController::class, 'scan']);
        Route::get('/participants/lookup', [AttendanceApiController::class, 'lookup']);
    });
});
