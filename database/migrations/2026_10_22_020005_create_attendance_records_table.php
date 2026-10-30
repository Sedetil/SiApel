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
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apel_schedule_id')->constrained('apel_schedules')->onDelete('cascade');
            $table->foreignId('participant_id')->constrained('participants')->onDelete('cascade');
            $table->foreignId('rfid_card_id')->nullable()->constrained('rfid_cards')->nullOnDelete();
            $table->timestamp('check_in_time')->nullable()->index();
            $table->timestamp('check_out_time')->nullable();
            $table->enum('status', ['hadir', 'terlambat', 'izin', 'sakit', 'alpa'])->default('hadir')->index();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('device_identifier', 100)->nullable();
            $table->string('notes', 255)->nullable();
            $table->timestamps();

            // Constraint unik: 1 peserta hanya memiliki 1 record absensi per jadwal apel
            $table->unique(['apel_schedule_id', 'participant_id'], 'unique_schedule_participant_attendance');
            $table->index(['apel_schedule_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_records');
    }
};
