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
        Schema::table('users', function (Blueprint $table) {
            $table->string('study_program', 150)->nullable()->after('role');
            $table->string('phone', 30)->nullable()->after('study_program');
            $table->string('avatar', 255)->nullable()->after('phone');
        });

        Schema::table('apel_schedules', function (Blueprint $table) {
            $table->enum('category', ['rutin', 'tambahan', 'khusus'])->default('rutin')->after('title')->index();
        });

        Schema::create('permission_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('participant_id')->constrained('participants')->onDelete('cascade');
            $table->foreignId('apel_schedule_id')->nullable()->constrained('apel_schedules')->nullOnDelete();
            $table->enum('type', ['izin', 'sakit'])->default('izin')->index();
            $table->text('reason');
            $table->string('attachment_path', 255)->nullable();
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu')->index();
            $table->foreignId('decided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('decided_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['status', 'type']);
        });

        Schema::create('ekuliah_sync_logs', function (Blueprint $table) {
            $table->id();
            $table->enum('sync_type', ['mahasiswa', 'kelas', 'jadwal', 'kehadiran'])->index();
            $table->enum('status', ['success', 'failed', 'partial'])->default('success')->index();
            $table->unsignedInteger('records_synced')->default(0);
            $table->string('message', 255)->nullable();
            $table->json('details')->nullable();
            $table->foreignId('synced_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique();
            $table->text('value')->nullable();
            $table->string('group', 50)->default('general')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
        Schema::dropIfExists('ekuliah_sync_logs');
        Schema::dropIfExists('permission_requests');

        Schema::table('apel_schedules', function (Blueprint $table) {
            $table->dropColumn('category');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['study_program', 'phone', 'avatar']);
        });
    }
};
