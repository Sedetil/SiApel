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
        Schema::create('participants', function (Blueprint $table) {
            $table->id();
            $table->string('identifier', 64)->unique()->comment('NPM / NIM peserta apel');
            $table->string('name', 255);
            $table->string('study_program', 100)->nullable()->comment('Program Studi / Jurusan');
            $table->string('class_group', 50)->nullable()->comment('Kompi / Pleton / Kelas');
            $table->enum('gender', ['L', 'P'])->nullable();
            $table->string('phone', 30)->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participants');
    }
};
