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
        Schema::create('apel_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->date('schedule_date')->index();
            $table->time('start_time');
            $table->time('end_time');
            $table->time('attendance_start');
            $table->time('attendance_end');
            $table->time('late_threshold_time');
            $table->string('location', 255)->default('Lapangan Apel POLINELA');
            $table->enum('status', ['scheduled', 'active', 'completed', 'cancelled'])->default('scheduled')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['schedule_date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apel_schedules');
    }
};
