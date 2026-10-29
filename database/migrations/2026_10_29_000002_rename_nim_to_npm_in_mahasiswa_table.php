<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('mahasiswa') && Schema::hasColumn('mahasiswa', 'nim')) {
            DB::statement("ALTER TABLE `mahasiswa` CHANGE COLUMN `nim` `npm` VARCHAR(64) NOT NULL COMMENT 'NPM peserta apel'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('mahasiswa') && Schema::hasColumn('mahasiswa', 'npm')) {
            DB::statement("ALTER TABLE `mahasiswa` CHANGE COLUMN `npm` `nim` VARCHAR(64) NOT NULL COMMENT 'NIM peserta apel'");
        }
    }
};
