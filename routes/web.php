<?php

use App\Http\Controllers\AcademicStructureController;
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

// Halaman Awal & Pengalihan Berdasarkan Peran
Route::get('/', function () {
    if (! auth()->check()) {
        return redirect()->route('login');
    }

    return auth()->user()->isAdmin()
        ? redirect()->route('admin.dashboard')
        : redirect()->route('timdis.dashboard');
});

// Autentikasi Publik
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| RUTE KHUSUS ADMINISTRATOR (/admin/...)
| Dilindungi Middleware: auth + role:admin
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {

    // 1. MENU UTAMA
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Riwayat Presensi & Laporan
    Route::get('/presensi/riwayat', [AttendanceController::class, 'history'])->name('attendance.history');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export-csv', [ReportController::class, 'exportCsv'])->name('reports.export-csv');
    Route::get('/reports/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.export-pdf');

    // Live Monitoring Apel
    Route::get('/monitoring', [AttendanceController::class, 'monitor'])->name('attendance.monitor');
    Route::get('/presensi/scan', [AttendanceController::class, 'monitor'])->name('attendance.scan');
    Route::get('/monitoring/feed', [AttendanceController::class, 'liveFeed'])->name('attendance.live-feed');

    // 2. VERIFIKASI & APPROVAL
    Route::get('/izin-sakit', [PermissionRequestController::class, 'index'])->name('permission-requests.index');
    Route::get('/izin-sakit/{permissionRequest}', [PermissionRequestController::class, 'show'])->name('permission-requests.show');
    Route::post('/izin-sakit/{permissionRequest}/decide', [PermissionRequestController::class, 'decide'])->name('permission-requests.decide');

    // 3. USER MANAGEMENT
    // Kelola Data Mahasiswa (CRUD Lengkap)
    Route::get('/participants', [ParticipantController::class, 'index'])->name('participants.index');
    Route::get('/mahasiswa', [ParticipantController::class, 'index'])->name('mahasiswa.index');
    Route::get('/participants/create', [ParticipantController::class, 'create'])->name('participants.create');
    Route::post('/participants', [ParticipantController::class, 'store'])->name('participants.store');
    Route::get('/participants/{participant}', [ParticipantController::class, 'show'])->whereNumber('participant')->name('participants.show');
    Route::get('/participants/{participant}/edit', [ParticipantController::class, 'edit'])->whereNumber('participant')->name('participants.edit');
    Route::put('/participants/{participant}', [ParticipantController::class, 'update'])->whereNumber('participant')->name('participants.update');
    Route::post('/participants/{participant}/toggle-status', [ParticipantController::class, 'toggleStatus'])->whereNumber('participant')->name('participants.toggle-status');
    Route::post('/participants/{participant}/map-rfid', [ParticipantController::class, 'mapRfid'])->whereNumber('participant')->name('participants.map-rfid');
    Route::post('/participants/{participant}/deactivate-rfid', [ParticipantController::class, 'deactivateRfid'])->whereNumber('participant')->name('participants.deactivate-rfid');
    Route::post('/participants/sync-ekuliah', [EKuliahController::class, 'syncFromParticipants'])->name('participants.sync-ekuliah');

    // Kelola Data Timdis (CRUD Lengkap)
    Route::get('/officers', [OfficerController::class, 'index'])->name('officers.index');
    Route::get('/timdis', [OfficerController::class, 'index'])->name('timdis.index');
    Route::post('/officers', [OfficerController::class, 'store'])->name('officers.store');
    Route::put('/officers/{officer}', [OfficerController::class, 'update'])->name('officers.update');
    Route::delete('/officers/{officer}', [OfficerController::class, 'destroy'])->name('officers.destroy');
    Route::post('/officers/{officer}/toggle-status', [OfficerController::class, 'toggleStatus'])->name('officers.toggle-status');

    // 4. PENGATURAN SISTEM
    // Kelola Jadwal Apel
    Route::get('/atur-jadwal', [ApelScheduleController::class, 'index'])->name('schedules.atur-jadwal');
    Route::get('/jadwal-khusus', [ApelScheduleController::class, 'index'])->name('schedules.khusus');
    Route::resource('schedules', ApelScheduleController::class);
    Route::post('/schedules/{schedule}/status', [ApelScheduleController::class, 'updateStatus'])->name('schedules.update-status');
    Route::post('/schedules/{schedule}/cancel', [ApelScheduleController::class, 'cancel'])->name('schedules.cancel');

    // Integrasi E-Kuliah
    Route::get('/integrasi-ekuliah', [EKuliahController::class, 'index'])->name('ekuliah.index');
    Route::post('/integrasi-ekuliah/config', [EKuliahController::class, 'updateConfig'])->name('ekuliah.update-config');
    Route::post('/integrasi-ekuliah/sync-data', [EKuliahController::class, 'syncData'])->name('ekuliah.sync-data');
    Route::post('/integrasi-ekuliah/sync-attendance', [EKuliahController::class, 'syncAttendance'])->name('ekuliah.sync-attendance');

    // Struktur Akademik (Jurusan, Program Studi, Kelas & Angkatan)
    Route::get('/struktur-akademik', [AcademicStructureController::class, 'index'])->name('academic-structures.index');
    Route::post('/struktur-akademik/department', [AcademicStructureController::class, 'storeDepartment'])->name('academic-structures.departments.store');
    Route::put('/struktur-akademik/department/{department}', [AcademicStructureController::class, 'updateDepartment'])->name('academic-structures.departments.update');
    Route::delete('/struktur-akademik/department/{department}', [AcademicStructureController::class, 'destroyDepartment'])->name('academic-structures.departments.destroy');
    Route::post('/struktur-akademik/study-program', [AcademicStructureController::class, 'storeStudyProgram'])->name('academic-structures.study-programs.store');
    Route::put('/struktur-akademik/study-program/{studyProgram}', [AcademicStructureController::class, 'updateStudyProgram'])->name('academic-structures.study-programs.update');
    Route::delete('/struktur-akademik/study-program/{studyProgram}', [AcademicStructureController::class, 'destroyStudyProgram'])->name('academic-structures.study-programs.destroy');
    Route::post('/struktur-akademik/class', [AcademicStructureController::class, 'storeClass'])->name('academic-structures.classes.store');
    Route::put('/struktur-akademik/class/{academicClass}', [AcademicStructureController::class, 'updateClass'])->name('academic-structures.classes.update');
    Route::delete('/struktur-akademik/class/{academicClass}', [AcademicStructureController::class, 'destroyClass'])->name('academic-structures.classes.destroy');

    // Audit Trail
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');

    // 5. AKUN ADMIN
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
});

/*
|--------------------------------------------------------------------------
| RUTE KHUSUS TIM KEDISIPLINAN (/timdis/...)
| Dilindungi Middleware: auth + role:timdis,petugas
|--------------------------------------------------------------------------
*/
Route::prefix('timdis')->name('timdis.')->middleware(['auth', 'role:timdis,petugas'])->group(function () {

    // 1. MENU UTAMA
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Riwayat Presensi & Laporan
    Route::get('/presensi/riwayat', [AttendanceController::class, 'history'])->name('attendance.history');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export-csv', [ReportController::class, 'exportCsv'])->name('reports.export-csv');
    Route::get('/reports/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.export-pdf');

    // Live Monitoring Apel
    Route::get('/monitoring', [AttendanceController::class, 'monitor'])->name('attendance.monitor');
    Route::get('/presensi/scan', [AttendanceController::class, 'monitor'])->name('attendance.scan');
    Route::get('/monitoring/feed', [AttendanceController::class, 'liveFeed'])->name('attendance.live-feed');

    // 2. VERIFIKASI & APPROVAL
    Route::get('/izin-sakit', [PermissionRequestController::class, 'index'])->name('permission-requests.index');
    Route::get('/izin-sakit/{permissionRequest}', [PermissionRequestController::class, 'show'])->name('permission-requests.show');
    Route::post('/izin-sakit/{permissionRequest}/decide', [PermissionRequestController::class, 'decide'])->name('permission-requests.decide');

    // 3. MAHASISWA BINAAN TIMDIS
    Route::get('/mahasiswa', [ParticipantController::class, 'index'])->name('participants');
    Route::get('/participants', [ParticipantController::class, 'index'])->name('participants.index');
    Route::get('/participants/{participant}', [ParticipantController::class, 'show'])->whereNumber('participant')->name('participants.show');

    // 4. AKUN TIMDIS
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
});

// Fallback rute lama agar pemanggilan route('dashboard'), route('attendance.history'), dll tetap berjalan
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Presensi, Live Monitoring & Laporan
    Route::get('/attendance/history', [AttendanceController::class, 'history'])->name('attendance.history');
    Route::get('/monitoring', [AttendanceController::class, 'monitor'])->name('attendance.monitor');
    Route::get('/presensi/scan', [AttendanceController::class, 'monitor'])->name('attendance.scan');
    Route::get('/monitoring/feed', [AttendanceController::class, 'liveFeed'])->name('attendance.live-feed');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export-csv', [ReportController::class, 'exportCsv'])->name('reports.export-csv');
    Route::get('/reports/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.export-pdf');

    // Verifikasi & Approval Izin/Sakit
    Route::get('/permission-requests', [PermissionRequestController::class, 'index'])->name('permission-requests.index');
    Route::get('/permission-requests/{permissionRequest}', [PermissionRequestController::class, 'show'])->name('permission-requests.show');
    Route::post('/permission-requests/{permissionRequest}/decide', [PermissionRequestController::class, 'decide'])->name('permission-requests.decide');

    // Data Mahasiswa
    Route::get('/participants', [ParticipantController::class, 'index'])->name('participants.index');
    Route::get('/participants/create', [ParticipantController::class, 'create'])->name('participants.create');
    Route::post('/participants', [ParticipantController::class, 'store'])->name('participants.store');
    Route::get('/participants/{participant}', [ParticipantController::class, 'show'])->whereNumber('participant')->name('participants.show');
    Route::get('/participants/{participant}/edit', [ParticipantController::class, 'edit'])->whereNumber('participant')->name('participants.edit');
    Route::put('/participants/{participant}', [ParticipantController::class, 'update'])->whereNumber('participant')->name('participants.update');
    Route::post('/participants/{participant}/toggle-status', [ParticipantController::class, 'toggleStatus'])->whereNumber('participant')->name('participants.toggle-status');
    Route::post('/participants/{participant}/map-rfid', [ParticipantController::class, 'mapRfid'])->whereNumber('participant')->name('participants.map-rfid');
    Route::post('/participants/{participant}/deactivate-rfid', [ParticipantController::class, 'deactivateRfid'])->whereNumber('participant')->name('participants.deactivate-rfid');
    Route::post('/participants/sync-ekuliah', [EKuliahController::class, 'syncFromParticipants'])->name('participants.sync-ekuliah');

    // Data Timdis
    Route::get('/officers', [OfficerController::class, 'index'])->name('officers.index');
    Route::post('/officers', [OfficerController::class, 'store'])->name('officers.store');
    Route::put('/officers/{officer}', [OfficerController::class, 'update'])->name('officers.update');
    Route::delete('/officers/{officer}', [OfficerController::class, 'destroy'])->name('officers.destroy');
    Route::post('/officers/{officer}/toggle-status', [OfficerController::class, 'toggleStatus'])->name('officers.toggle-status');

    // Jadwal Apel
    Route::resource('schedules', ApelScheduleController::class);
    Route::post('/schedules/{schedule}/status', [ApelScheduleController::class, 'updateStatus'])->name('schedules.update-status');
    Route::post('/schedules/{schedule}/cancel', [ApelScheduleController::class, 'cancel'])->name('schedules.cancel');

    // Struktur Akademik
    Route::get('/academic-structures', [AcademicStructureController::class, 'index'])->name('academic-structures.index');
    Route::post('/academic-structures/department', [AcademicStructureController::class, 'storeDepartment'])->name('academic-structures.departments.store');
    Route::put('/academic-structures/department/{department}', [AcademicStructureController::class, 'updateDepartment'])->name('academic-structures.departments.update');
    Route::delete('/academic-structures/department/{department}', [AcademicStructureController::class, 'destroyDepartment'])->name('academic-structures.departments.destroy');
    Route::post('/academic-structures/study-program', [AcademicStructureController::class, 'storeStudyProgram'])->name('academic-structures.study-programs.store');
    Route::put('/academic-structures/study-program/{studyProgram}', [AcademicStructureController::class, 'updateStudyProgram'])->name('academic-structures.study-programs.update');
    Route::delete('/academic-structures/study-program/{studyProgram}', [AcademicStructureController::class, 'destroyStudyProgram'])->name('academic-structures.study-programs.destroy');
    Route::post('/academic-structures/class', [AcademicStructureController::class, 'storeClass'])->name('academic-structures.classes.store');
    Route::put('/academic-structures/class/{academicClass}', [AcademicStructureController::class, 'updateClass'])->name('academic-structures.classes.update');
    Route::delete('/academic-structures/class/{academicClass}', [AcademicStructureController::class, 'destroyClass'])->name('academic-structures.classes.destroy');

    // Integrasi E-Kuliah & Audit Trail
    Route::get('/ekuliah', [EKuliahController::class, 'index'])->name('ekuliah.index');
    Route::post('/ekuliah/config', [EKuliahController::class, 'updateConfig'])->name('ekuliah.update-config');
    Route::post('/ekuliah/sync-data', [EKuliahController::class, 'syncData'])->name('ekuliah.sync-data');
    Route::post('/ekuliah/sync-attendance', [EKuliahController::class, 'syncAttendance'])->name('ekuliah.sync-attendance');
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');

    // Profil Pengguna
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
});
