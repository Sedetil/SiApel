<?php

use App\Http\Controllers\Api\AttendanceApiController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SyncController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — SIABSEN POLINELA
|--------------------------------------------------------------------------
*/

// Endpoint Sinkronisasi Sistem Presensi Lokal Python <-> Server Online
Route::get('/kegiatan', [SyncController::class, 'kegiatan']);
Route::options('/sync', [SyncController::class, 'options']);
Route::post('/sync', [SyncController::class, 'pushAttendance'])->name('api.sync.push');

Route::prefix('sync')->group(function () {
    Route::get('/status', [SyncController::class, 'status'])->name('api.sync.status');
    Route::get('/mahasiswa', [SyncController::class, 'mahasiswa'])->name('api.sync.mahasiswa');
    Route::get('/schedules', [SyncController::class, 'schedules'])->name('api.sync.schedules');
    Route::get('/kegiatan', [SyncController::class, 'kegiatan'])->name('api.sync.kegiatan');
    Route::get('/system-config', [SyncController::class, 'systemConfig'])->name('api.sync.system-config');
});

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
