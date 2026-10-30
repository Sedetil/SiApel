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
        $userTable = Schema::hasTable('pengguna') ? 'pengguna' : 'users';
        $participantTable = Schema::hasTable('mahasiswa') ? 'mahasiswa' : 'participants';

        Schema::table($userTable, function (Blueprint $table) {
            $table->string('cohort_year', 100)->nullable()->change();
        });

        Schema::table($participantTable, function (Blueprint $table) {
            $table->string('cohort_year', 50)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $userTable = Schema::hasTable('pengguna') ? 'pengguna' : 'users';
        $participantTable = Schema::hasTable('mahasiswa') ? 'mahasiswa' : 'participants';

        Schema::table($userTable, function (Blueprint $table) {
            $table->string('cohort_year', 10)->nullable()->change();
        });

        Schema::table($participantTable, function (Blueprint $table) {
            $table->string('cohort_year', 10)->nullable()->change();
        });
    }
};
