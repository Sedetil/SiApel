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
        // 1. Jurusan (Departments)
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150)->unique();
            $table->string('code', 50)->nullable()->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Program Studi (Study Programs) -> berelasi ke Department
        Schema::create('study_programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained('departments')->cascadeOnDelete();
            $table->string('name', 150);
            $table->string('code', 50)->nullable();
            $table->string('degree_level', 20)->default('D3'); // D3, D4/Sarjana Terapan, S2 Terapan
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. Kelas (Classes) -> berelasi ke Study Program
        Schema::create('academic_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('study_program_id')->constrained('study_programs')->cascadeOnDelete();
            $table->string('name', 50); // Contoh: MI 3A, TRPL 1B
            $table->string('cohort_year', 10); // Contoh: 2024, 2025
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_classes');
        Schema::dropIfExists('study_programs');
        Schema::dropIfExists('departments');
    }
};
