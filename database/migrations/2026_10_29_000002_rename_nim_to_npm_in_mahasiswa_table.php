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
        if (Schema::hasColumn('mahasiswa', 'nim')) {
            DB::statement('ALTER TABLE `mahasiswa` RENAME COLUMN `nim` TO `npm`');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('mahasiswa', 'npm')) {
            DB::statement('ALTER TABLE `mahasiswa` RENAME COLUMN `npm` TO `nim`');
        }
    }
};

