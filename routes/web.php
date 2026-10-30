<?php

use App\Http\Controllers\ApelScheduleController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EKuliahController;
use App\Http\Controllers\OfficerController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\PermissionRequestController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — SIABSEN POLINELA RFID
|--------------------------------------------------------------------------
*/

// Halaman Awal
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Autentikasi Web
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rute Terproteksi Sesi Web
Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | 1. MENU UTAMA
    |--------------------------------------------------------------------------
    */
    // Dashboard Monitoring Utama
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Riwayat Absen & Rekap Kehadiran Seluruh Mahasiswa
    Route::get('/presensi/riwayat', [AttendanceController::class, 'history'])->name('attendance.history');
    Route::get('/reports/export-csv', [ReportController::class, 'exportCsv'])->name('reports.export-csv');
    Route::get('/reports/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.export-pdf');

    // Live Monitoring Sesi Apel (Read-Only)
    Route::get('/monitoring', [AttendanceController::class, 'monitor'])->name('attendance.monitor');
    Route::get('/presensi/scan', [AttendanceController::class, 'monitor']);
    Route::get('/monitoring/feed', [AttendanceController::class, 'liveFeed'])->name('attendance.live-feed');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    /*
    |--------------------------------------------------------------------------
    | 2. VERIFIKASI & APPROVAL
    |--------------------------------------------------------------------------
    */
    // Izin & Sakit (Pantau pengajuan mahasiswa)
    Route::get('/izin-sakit', [PermissionRequestController::class, 'index'])->name('permission-requests.index');
    Route::get('/izin-sakit/{permissionRequest}', [PermissionRequestController::class, 'show'])->name('permission-requests.show');
    Route::post('/izin-sakit/{permissionRequest}/decide', [PermissionRequestController::class, 'decide'])->name('permission-requests.decide');

    /*
    |--------------------------------------------------------------------------
    | 3. USER MANAGEMENT
    |--------------------------------------------------------------------------
    */
    // Mahasiswa Detail (Read-Only untuk semua role)
    Route::get('/participants/{participant}', [ParticipantController::class, 'show'])->whereNumber('participant')->name('participants.show');

    // Modul Khusus Administrator
    Route::middleware(['role:admin'])->group(function () {
        // Mahasiswa (Data berasal dari E-Kuliah)
        Route::get('/participants', [ParticipantController::class, 'index'])->name('participants.index');
        Route::get('/participants/create', [ParticipantController::class, 'create'])->name('participants.create');
        Route::post('/participants', [ParticipantController::class, 'store'])->name('participants.store');
        Route::get('/participants/{participant}/edit', [ParticipantController::class, 'edit'])->whereNumber('participant')->name('participants.edit');
        Route::put('/participants/{participant}', [ParticipantController::class, 'update'])->whereNumber('participant')->name('participants.update');
        Route::post('/participants/{participant}/toggle-status', [ParticipantController::class, 'toggleStatus'])->whereNumber('participant')->name('participants.toggle-status');
        Route::post('/participants/{participant}/map-rfid', [ParticipantController::class, 'mapRfid'])->whereNumber('participant')->name('participants.map-rfid');
        Route::post('/participants/{participant}/deactivate-rfid', [ParticipantController::class, 'deactivateRfid'])->whereNumber('participant')->name('participants.deactivate-rfid');
        Route::post('/participants/sync-ekuliah', [EKuliahController::class, 'syncFromParticipants'])->name('participants.sync-ekuliah');

        // Timdis (Kelola akun petugas)
        Route::get('/officers', [OfficerController::class, 'index'])->name('officers.index');
        Route::post('/officers', [OfficerController::class, 'store'])->name('officers.store');
        Route::put('/officers/{officer}', [OfficerController::class, 'update'])->name('officers.update');
        Route::delete('/officers/{officer}', [OfficerController::class, 'destroy'])->name('officers.destroy');
        Route::post('/officers/{officer}/toggle-status', [OfficerController::class, 'toggleStatus'])->name('officers.toggle-status');

        /*
        |--------------------------------------------------------------------------
        | 4. PENGATURAN SISTEM
        |--------------------------------------------------------------------------
        */
        // Kelola Apel (Jadwal dan aturan presensi)
        Route::resource('schedules', ApelScheduleController::class);
        Route::post('/schedules/{schedule}/status', [ApelScheduleController::class, 'updateStatus'])->name('schedules.update-status');
        Route::post('/schedules/{schedule}/cancel', [ApelScheduleController::class, 'cancel'])->name('schedules.cancel');

        // Integrasi E-Kuliah (Hubungan dengan sistem E-Kuliah)
        Route::get('/integrasi-ekuliah', [EKuliahController::class, 'index'])->name('ekuliah.index');
        Route::post('/integrasi-ekuliah/config', [EKuliahController::class, 'updateConfig'])->name('ekuliah.update-config');
        Route::post('/integrasi-ekuliah/sync-data', [EKuliahController::class, 'syncData'])->name('ekuliah.sync-data');
        Route::post('/integrasi-ekuliah/sync-attendance', [EKuliahController::class, 'syncAttendance'])->name('ekuliah.sync-attendance');

        // Audit Trail (Jejak aktivitas pengguna)
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    });

    /*
    |--------------------------------------------------------------------------
    | 5. AKUN
    |--------------------------------------------------------------------------
    */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
});
