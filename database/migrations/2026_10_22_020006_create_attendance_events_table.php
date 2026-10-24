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
        Schema::create('attendance_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apel_schedule_id')->nullable()->constrained('apel_schedules')->nullOnDelete();
            $table->string('card_uid', 64)->index();
            $table->foreignId('participant_id')->nullable()->constrained('participants')->nullOnDelete();
            $table->enum('scan_type', ['check_in', 'check_out'])->default('check_in');
            $table->timestamp('scanned_at')->useCurrent()->index();
            $table->enum('result', ['success', 'rejected'])->index();
            $table->string('status_determined', 30)->nullable();
            $table->string('rejection_reason', 100)->nullable();
            $table->string('device_identifier', 100)->nullable();
            $table->json('raw_payload')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_events');
    }
};
