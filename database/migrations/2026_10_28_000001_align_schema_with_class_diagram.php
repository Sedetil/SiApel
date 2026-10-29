<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tambah atribut semester & kode_qr ke tabel participants (Mahasiswa)
        Schema::table('participants', function (Blueprint $table) {
            $table->unsignedTinyInteger('semester')->nullable()->after('class_group')->comment('Semester akademik berjalan');
            $table->string('kode_qr', 255)->nullable()->after('semester')->comment('String payload / hash Kode QR mahasiswa');
        });

        // 2. Tambah atribut metode & menit_terlambat ke tabel attendance_records (Presensi)
        Schema::table('attendance_records', function (Blueprint $table) {
            $table->string('metode', 50)->default('RFID')->after('check_out_time')->comment('Metode verifikasi presensi (RFID / QR / Manual)');
            $table->unsignedInteger('menit_terlambat')->default(0)->after('status')->comment('Durasi keterlambatan dalam menit');
        });

        // 3. Tambah atribut alasan_penolakan ke tabel permission_requests (PengajuanIzin)
        Schema::table('permission_requests', function (Blueprint $table) {
            $table->text('alasan_penolakan')->nullable()->after('notes')->comment('Alasan eksplisit penolakan izin oleh Timdis/Admin');
        });

        // 4. Tambah atribut jurusan_binaan ke tabel users (Timdis)
        Schema::table('users', function (Blueprint $table) {
            $table->string('jurusan_binaan', 150)->nullable()->after('study_program')->comment('Jurusan yang dibina oleh personil Timdis');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('jurusan_binaan');
        });

        Schema::table('permission_requests', function (Blueprint $table) {
            $table->dropColumn('alasan_penolakan');
        });

        Schema::table('attendance_records', function (Blueprint $table) {
            $table->dropColumn(['metode', 'menit_terlambat']);
        });

        Schema::table('participants', function (Blueprint $table) {
            $table->dropColumn(['semester', 'kode_qr']);
        });
    }
};
