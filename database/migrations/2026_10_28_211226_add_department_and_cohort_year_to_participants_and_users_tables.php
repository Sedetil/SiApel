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
        Schema::table('participants', function (Blueprint $table) {
            $table->string('department', 150)->nullable()->after('name')->comment('Jurusan dari E-Kuliah');
            $table->string('cohort_year', 10)->nullable()->after('class_group')->comment('Angkatan Mahasiswa');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('department', 150)->nullable()->after('role')->comment('Scope Jurusan Timdis dari E-Kuliah');
            $table->string('class_group', 50)->nullable()->after('study_program')->comment('Scope Kelas/Kompi Timdis dari E-Kuliah');
            $table->string('cohort_year', 10)->nullable()->after('class_group')->comment('Scope Angkatan Timdis dari E-Kuliah');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->dropColumn(['department', 'cohort_year']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['department', 'class_group', 'cohort_year']);
        });
    }
};
