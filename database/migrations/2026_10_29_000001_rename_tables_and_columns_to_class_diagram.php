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
        // 1. Drop existing Foreign Keys that reference tables/columns to be renamed
        DB::statement('ALTER TABLE `apel_schedules` DROP FOREIGN KEY `apel_schedules_created_by_foreign`');
        DB::statement('ALTER TABLE `attendance_events` DROP FOREIGN KEY `attendance_events_apel_schedule_id_foreign`');
        DB::statement('ALTER TABLE `attendance_events` DROP FOREIGN KEY `attendance_events_participant_id_foreign`');
        DB::statement('ALTER TABLE `attendance_records` DROP FOREIGN KEY `attendance_records_apel_schedule_id_foreign`');
        DB::statement('ALTER TABLE `attendance_records` DROP FOREIGN KEY `attendance_records_participant_id_foreign`');
        DB::statement('ALTER TABLE `attendance_records` DROP FOREIGN KEY `attendance_records_recorded_by_foreign`');
        DB::statement('ALTER TABLE `attendance_records` DROP FOREIGN KEY `attendance_records_rfid_card_id_foreign`');
        DB::statement('ALTER TABLE `audit_logs` DROP FOREIGN KEY `audit_logs_user_id_foreign`');
        DB::statement('ALTER TABLE `ekuliah_sync_logs` DROP FOREIGN KEY `ekuliah_sync_logs_synced_by_foreign`');
        DB::statement('ALTER TABLE `permission_requests` DROP FOREIGN KEY `permission_requests_apel_schedule_id_foreign`');
        DB::statement('ALTER TABLE `permission_requests` DROP FOREIGN KEY `permission_requests_decided_by_foreign`');
        DB::statement('ALTER TABLE `permission_requests` DROP FOREIGN KEY `permission_requests_participant_id_foreign`');
        DB::statement('ALTER TABLE `rfid_cards` DROP FOREIGN KEY `rfid_cards_participant_id_foreign`');

        // 2. Rename columns and table: users -> pengguna
        DB::statement('ALTER TABLE `users` RENAME COLUMN `id` TO `id_pengguna`');
        DB::statement('ALTER TABLE `users` RENAME COLUMN `name` TO `nama`');
        DB::statement('ALTER TABLE `users` RENAME COLUMN `password` TO `kata_sandi`');
        DB::statement('ALTER TABLE `users` RENAME COLUMN `avatar` TO `foto_profil`');
        DB::statement('ALTER TABLE `users` RENAME COLUMN `role` TO `peran`');
        DB::statement('ALTER TABLE `users` RENAME COLUMN `study_program` TO `prodi_binaan`');
        DB::statement('RENAME TABLE `users` TO `pengguna`');

        // 3. Rename columns and table: participants -> mahasiswa
        DB::statement('ALTER TABLE `participants` RENAME COLUMN `id` TO `id_mahasiswa`');
        DB::statement('ALTER TABLE `participants` RENAME COLUMN `identifier` TO `nim`');
        DB::statement('ALTER TABLE `participants` RENAME COLUMN `name` TO `nama`');
        DB::statement('ALTER TABLE `participants` RENAME COLUMN `class_group` TO `kelas`');
        if (! Schema::hasColumn('participants', 'id_rfid')) {
            DB::statement('ALTER TABLE `participants` ADD COLUMN `id_rfid` BIGINT UNSIGNED NULL AFTER `kode_qr`');
        }
        DB::statement('RENAME TABLE `participants` TO `mahasiswa`');

        // 4. Rename columns and table: apel_schedules -> apel
        DB::statement('ALTER TABLE `apel_schedules` RENAME COLUMN `id` TO `id_apel`');
        DB::statement('ALTER TABLE `apel_schedules` RENAME COLUMN `schedule_date` TO `tanggal`');
        DB::statement('ALTER TABLE `apel_schedules` RENAME COLUMN `start_time` TO `jam_mulai`');
        DB::statement('ALTER TABLE `apel_schedules` RENAME COLUMN `end_time` TO `jam_selesai`');
        DB::statement('ALTER TABLE `apel_schedules` RENAME COLUMN `category` TO `jenis`');
        DB::statement('RENAME TABLE `apel_schedules` TO `apel`');

        // 5. Rename columns and table: attendance_records -> presensi
        DB::statement('ALTER TABLE `attendance_records` RENAME COLUMN `id` TO `id_presensi`');
        DB::statement('ALTER TABLE `attendance_records` RENAME COLUMN `apel_schedule_id` TO `id_apel`');
        DB::statement('ALTER TABLE `attendance_records` RENAME COLUMN `participant_id` TO `id_mahasiswa`');
        DB::statement('ALTER TABLE `attendance_records` RENAME COLUMN `check_in_time` TO `waktu_scan`');
        DB::statement('RENAME TABLE `attendance_records` TO `presensi`');

        // 6. Rename columns and table: permission_requests -> pengajuan_izin
        DB::statement('ALTER TABLE `permission_requests` RENAME COLUMN `id` TO `id_izin`');
        DB::statement('ALTER TABLE `permission_requests` RENAME COLUMN `participant_id` TO `id_mahasiswa`');
        DB::statement('ALTER TABLE `permission_requests` RENAME COLUMN `apel_schedule_id` TO `id_apel`');
        DB::statement('ALTER TABLE `permission_requests` RENAME COLUMN `decided_by` TO `id_timdis`');
        DB::statement('ALTER TABLE `permission_requests` RENAME COLUMN `type` TO `jenis`');
        DB::statement('ALTER TABLE `permission_requests` RENAME COLUMN `reason` TO `alasan`');
        DB::statement('ALTER TABLE `permission_requests` RENAME COLUMN `attachment_path` TO `berkas_bukti`');
        DB::statement('RENAME TABLE `permission_requests` TO `pengajuan_izin`');

        // 7. Rename columns and table: ekuliah_sync_logs -> log_sinkronisasi
        DB::statement('ALTER TABLE `ekuliah_sync_logs` RENAME COLUMN `id` TO `id_sinkron`');
        DB::statement('ALTER TABLE `ekuliah_sync_logs` RENAME COLUMN `records_synced` TO `jumlah_data`');
        DB::statement('ALTER TABLE `ekuliah_sync_logs` RENAME COLUMN `synced_by` TO `id_admin`');
        if (! Schema::hasColumn('ekuliah_sync_logs', 'waktu')) {
            DB::statement('ALTER TABLE `ekuliah_sync_logs` ADD COLUMN `waktu` DATETIME NULL AFTER `id_admin`');
            DB::statement('UPDATE `ekuliah_sync_logs` SET `waktu` = `created_at`');
        }
        DB::statement('RENAME TABLE `ekuliah_sync_logs` TO `log_sinkronisasi`');

        // 8. Update FK column names in other related tables
        DB::statement('ALTER TABLE `attendance_events` RENAME COLUMN `apel_schedule_id` TO `id_apel`');
        DB::statement('ALTER TABLE `attendance_events` RENAME COLUMN `participant_id` TO `id_mahasiswa`');
        DB::statement('ALTER TABLE `rfid_cards` RENAME COLUMN `participant_id` TO `id_mahasiswa`');
        DB::statement('ALTER TABLE `audit_logs` RENAME COLUMN `user_id` TO `id_pengguna`');

        // 9. Re-add foreign keys pointing to new tables & columns
        DB::statement('ALTER TABLE `apel` ADD CONSTRAINT `apel_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE SET NULL');
        DB::statement('ALTER TABLE `presensi` ADD CONSTRAINT `presensi_id_apel_foreign` FOREIGN KEY (`id_apel`) REFERENCES `apel` (`id_apel`) ON DELETE CASCADE');
        DB::statement('ALTER TABLE `presensi` ADD CONSTRAINT `presensi_id_mahasiswa_foreign` FOREIGN KEY (`id_mahasiswa`) REFERENCES `mahasiswa` (`id_mahasiswa`) ON DELETE CASCADE');
        DB::statement('ALTER TABLE `presensi` ADD CONSTRAINT `presensi_recorded_by_foreign` FOREIGN KEY (`recorded_by`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE SET NULL');
        DB::statement('ALTER TABLE `presensi` ADD CONSTRAINT `presensi_rfid_card_id_foreign` FOREIGN KEY (`rfid_card_id`) REFERENCES `rfid_cards` (`id`) ON DELETE SET NULL');
        DB::statement('ALTER TABLE `pengajuan_izin` ADD CONSTRAINT `pengajuan_izin_id_apel_foreign` FOREIGN KEY (`id_apel`) REFERENCES `apel` (`id_apel`) ON DELETE SET NULL');
        DB::statement('ALTER TABLE `pengajuan_izin` ADD CONSTRAINT `pengajuan_izin_id_mahasiswa_foreign` FOREIGN KEY (`id_mahasiswa`) REFERENCES `mahasiswa` (`id_mahasiswa`) ON DELETE CASCADE');
        DB::statement('ALTER TABLE `pengajuan_izin` ADD CONSTRAINT `pengajuan_izin_id_timdis_foreign` FOREIGN KEY (`id_timdis`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE SET NULL');
        DB::statement('ALTER TABLE `log_sinkronisasi` ADD CONSTRAINT `log_sinkronisasi_id_admin_foreign` FOREIGN KEY (`id_admin`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE SET NULL');
        DB::statement('ALTER TABLE `attendance_events` ADD CONSTRAINT `attendance_events_id_apel_foreign` FOREIGN KEY (`id_apel`) REFERENCES `apel` (`id_apel`) ON DELETE CASCADE');
        DB::statement('ALTER TABLE `attendance_events` ADD CONSTRAINT `attendance_events_id_mahasiswa_foreign` FOREIGN KEY (`id_mahasiswa`) REFERENCES `mahasiswa` (`id_mahasiswa`) ON DELETE CASCADE');
        DB::statement('ALTER TABLE `rfid_cards` ADD CONSTRAINT `rfid_cards_id_mahasiswa_foreign` FOREIGN KEY (`id_mahasiswa`) REFERENCES `mahasiswa` (`id_mahasiswa`) ON DELETE CASCADE');
        DB::statement('ALTER TABLE `audit_logs` ADD CONSTRAINT `audit_logs_id_pengguna_foreign` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE SET NULL');

        // Populate id_rfid on mahasiswa if active rfid_card exists
        DB::statement('UPDATE `mahasiswa` m JOIN `rfid_cards` r ON r.id_mahasiswa = m.id_mahasiswa AND r.is_active = 1 SET m.id_rfid = r.id');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop new foreign keys
        DB::statement('ALTER TABLE `apel` DROP FOREIGN KEY `apel_created_by_foreign`');
        DB::statement('ALTER TABLE `presensi` DROP FOREIGN KEY `presensi_id_apel_foreign`');
        DB::statement('ALTER TABLE `presensi` DROP FOREIGN KEY `presensi_id_mahasiswa_foreign`');
        DB::statement('ALTER TABLE `presensi` DROP FOREIGN KEY `presensi_recorded_by_foreign`');
        DB::statement('ALTER TABLE `presensi` DROP FOREIGN KEY `presensi_rfid_card_id_foreign`');
        DB::statement('ALTER TABLE `pengajuan_izin` DROP FOREIGN KEY `pengajuan_izin_id_apel_foreign`');
        DB::statement('ALTER TABLE `pengajuan_izin` DROP FOREIGN KEY `pengajuan_izin_id_mahasiswa_foreign`');
        DB::statement('ALTER TABLE `pengajuan_izin` DROP FOREIGN KEY `pengajuan_izin_id_timdis_foreign`');
        DB::statement('ALTER TABLE `log_sinkronisasi` DROP FOREIGN KEY `log_sinkronisasi_id_admin_foreign`');
        DB::statement('ALTER TABLE `attendance_events` DROP FOREIGN KEY `attendance_events_id_apel_foreign`');
        DB::statement('ALTER TABLE `attendance_events` DROP FOREIGN KEY `attendance_events_id_mahasiswa_foreign`');
        DB::statement('ALTER TABLE `rfid_cards` DROP FOREIGN KEY `rfid_cards_id_mahasiswa_foreign`');
        DB::statement('ALTER TABLE `audit_logs` DROP FOREIGN KEY `audit_logs_id_pengguna_foreign`');

        // Revert tables and column names
        DB::statement('ALTER TABLE `audit_logs` RENAME COLUMN `id_pengguna` TO `user_id`');
        DB::statement('ALTER TABLE `rfid_cards` RENAME COLUMN `id_mahasiswa` TO `participant_id`');
        DB::statement('ALTER TABLE `attendance_events` RENAME COLUMN `id_apel` TO `apel_schedule_id`');
        DB::statement('ALTER TABLE `attendance_events` RENAME COLUMN `id_mahasiswa` TO `participant_id`');

        DB::statement('RENAME TABLE `log_sinkronisasi` TO `ekuliah_sync_logs`');
        DB::statement('ALTER TABLE `ekuliah_sync_logs` RENAME COLUMN `id_sinkron` TO `id`');
        DB::statement('ALTER TABLE `ekuliah_sync_logs` RENAME COLUMN `jumlah_data` TO `records_synced`');
        DB::statement('ALTER TABLE `ekuliah_sync_logs` RENAME COLUMN `id_admin` TO `synced_by`');

        DB::statement('RENAME TABLE `pengajuan_izin` TO `permission_requests`');
        DB::statement('ALTER TABLE `permission_requests` RENAME COLUMN `id_izin` TO `id`');
        DB::statement('ALTER TABLE `permission_requests` RENAME COLUMN `id_mahasiswa` TO `participant_id`');
        DB::statement('ALTER TABLE `permission_requests` RENAME COLUMN `id_apel` TO `apel_schedule_id`');
        DB::statement('ALTER TABLE `permission_requests` RENAME COLUMN `id_timdis` TO `decided_by`');
        DB::statement('ALTER TABLE `permission_requests` RENAME COLUMN `jenis` TO `type`');
        DB::statement('ALTER TABLE `permission_requests` RENAME COLUMN `alasan` TO `reason`');
        DB::statement('ALTER TABLE `permission_requests` RENAME COLUMN `berkas_bukti` TO `attachment_path`');

        DB::statement('RENAME TABLE `presensi` TO `attendance_records`');
        DB::statement('ALTER TABLE `attendance_records` RENAME COLUMN `id_presensi` TO `id`');
        DB::statement('ALTER TABLE `attendance_records` RENAME COLUMN `id_apel` TO `apel_schedule_id`');
        DB::statement('ALTER TABLE `attendance_records` RENAME COLUMN `id_mahasiswa` TO `participant_id`');
        DB::statement('ALTER TABLE `attendance_records` RENAME COLUMN `waktu_scan` TO `check_in_time`');

        DB::statement('RENAME TABLE `apel` TO `apel_schedules`');
        DB::statement('ALTER TABLE `apel_schedules` RENAME COLUMN `id_apel` TO `id`');
        DB::statement('ALTER TABLE `apel_schedules` RENAME COLUMN `tanggal` TO `schedule_date`');
        DB::statement('ALTER TABLE `apel_schedules` RENAME COLUMN `jam_mulai` TO `start_time`');
        DB::statement('ALTER TABLE `apel_schedules` RENAME COLUMN `jam_selesai` TO `end_time`');
        DB::statement('ALTER TABLE `apel_schedules` RENAME COLUMN `jenis` TO `category`');

        DB::statement('RENAME TABLE `mahasiswa` TO `participants`');
        DB::statement('ALTER TABLE `participants` RENAME COLUMN `id_mahasiswa` TO `id`');
        DB::statement('ALTER TABLE `participants` RENAME COLUMN `nim` TO `identifier`');
        DB::statement('ALTER TABLE `participants` RENAME COLUMN `nama` TO `name`');
        DB::statement('ALTER TABLE `participants` RENAME COLUMN `kelas` TO `class_group`');

        DB::statement('RENAME TABLE `pengguna` TO `users`');
        DB::statement('ALTER TABLE `users` RENAME COLUMN `id_pengguna` TO `id`');
        DB::statement('ALTER TABLE `users` RENAME COLUMN `nama` TO `name`');
        DB::statement('ALTER TABLE `users` RENAME COLUMN `kata_sandi` TO `password`');
        DB::statement('ALTER TABLE `users` RENAME COLUMN `foto_profil` TO `avatar`');
        DB::statement('ALTER TABLE `users` RENAME COLUMN `peran` TO `role`');
        DB::statement('ALTER TABLE `users` RENAME COLUMN `prodi_binaan` TO `study_program`');

        // Re-add original foreign keys
        DB::statement('ALTER TABLE `apel_schedules` ADD CONSTRAINT `apel_schedules_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL');
        DB::statement('ALTER TABLE `attendance_events` ADD CONSTRAINT `attendance_events_apel_schedule_id_foreign` FOREIGN KEY (`apel_schedule_id`) REFERENCES `apel_schedules` (`id`) ON DELETE CASCADE');
        DB::statement('ALTER TABLE `attendance_events` ADD CONSTRAINT `attendance_events_participant_id_foreign` FOREIGN KEY (`participant_id`) REFERENCES `participants` (`id`) ON DELETE CASCADE');
        DB::statement('ALTER TABLE `attendance_records` ADD CONSTRAINT `attendance_records_apel_schedule_id_foreign` FOREIGN KEY (`apel_schedule_id`) REFERENCES `apel_schedules` (`id`) ON DELETE CASCADE');
        DB::statement('ALTER TABLE `attendance_records` ADD CONSTRAINT `attendance_records_participant_id_foreign` FOREIGN KEY (`participant_id`) REFERENCES `participants` (`id`) ON DELETE CASCADE');
        DB::statement('ALTER TABLE `attendance_records` ADD CONSTRAINT `attendance_records_recorded_by_foreign` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL');
        DB::statement('ALTER TABLE `attendance_records` ADD CONSTRAINT `attendance_records_rfid_card_id_foreign` FOREIGN KEY (`rfid_card_id`) REFERENCES `rfid_cards` (`id`) ON DELETE SET NULL');
        DB::statement('ALTER TABLE `audit_logs` ADD CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL');
        DB::statement('ALTER TABLE `ekuliah_sync_logs` ADD CONSTRAINT `ekuliah_sync_logs_synced_by_foreign` FOREIGN KEY (`synced_by`) REFERENCES `users` (`id`) ON DELETE SET NULL');
        DB::statement('ALTER TABLE `permission_requests` ADD CONSTRAINT `permission_requests_apel_schedule_id_foreign` FOREIGN KEY (`apel_schedule_id`) REFERENCES `apel_schedules` (`id`) ON DELETE SET NULL');
        DB::statement('ALTER TABLE `permission_requests` ADD CONSTRAINT `permission_requests_decided_by_foreign` FOREIGN KEY (`decided_by`) REFERENCES `users` (`id`) ON DELETE SET NULL');
        DB::statement('ALTER TABLE `permission_requests` ADD CONSTRAINT `permission_requests_participant_id_foreign` FOREIGN KEY (`participant_id`) REFERENCES `participants` (`id`) ON DELETE CASCADE');
        DB::statement('ALTER TABLE `rfid_cards` ADD CONSTRAINT `rfid_cards_participant_id_foreign` FOREIGN KEY (`participant_id`) REFERENCES `participants` (`id`) ON DELETE CASCADE');
    }
};
