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
        // 1. Drop existing Foreign Keys safely (DROP CONSTRAINT IF EXISTS in MariaDB)
        $foreignKeysToDrop = [
            ['table' => 'apel_schedules', 'fk' => 'apel_schedules_created_by_foreign'],
            ['table' => 'attendance_events', 'fk' => 'attendance_events_apel_schedule_id_foreign'],
            ['table' => 'attendance_events', 'fk' => 'attendance_events_participant_id_foreign'],
            ['table' => 'attendance_records', 'fk' => 'attendance_records_apel_schedule_id_foreign'],
            ['table' => 'attendance_records', 'fk' => 'attendance_records_participant_id_foreign'],
            ['table' => 'attendance_records', 'fk' => 'attendance_records_recorded_by_foreign'],
            ['table' => 'attendance_records', 'fk' => 'attendance_records_rfid_card_id_foreign'],
            ['table' => 'audit_logs', 'fk' => 'audit_logs_user_id_foreign'],
            ['table' => 'ekuliah_sync_logs', 'fk' => 'ekuliah_sync_logs_synced_by_foreign'],
            ['table' => 'permission_requests', 'fk' => 'permission_requests_apel_schedule_id_foreign'],
            ['table' => 'permission_requests', 'fk' => 'permission_requests_decided_by_foreign'],
            ['table' => 'permission_requests', 'fk' => 'permission_requests_participant_id_foreign'],
            ['table' => 'rfid_cards', 'fk' => 'rfid_cards_participant_id_foreign'],
        ];

        foreach ($foreignKeysToDrop as $item) {
            if (Schema::hasTable($item['table'])) {
                DB::statement("ALTER TABLE `{$item['table']}` DROP CONSTRAINT IF EXISTS `{$item['fk']}`");
            }
        }

        // 2. Rename columns and table: users -> pengguna
        if (Schema::hasTable('users')) {
            if (Schema::hasColumn('users', 'id')) {
                DB::statement('ALTER TABLE `users` CHANGE COLUMN `id` `id_pengguna` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
            }
            if (Schema::hasColumn('users', 'name')) {
                DB::statement('ALTER TABLE `users` CHANGE COLUMN `name` `nama` VARCHAR(255) NOT NULL');
            }
            if (Schema::hasColumn('users', 'password')) {
                DB::statement('ALTER TABLE `users` CHANGE COLUMN `password` `kata_sandi` VARCHAR(255) NOT NULL');
            }
            if (Schema::hasColumn('users', 'avatar')) {
                DB::statement('ALTER TABLE `users` CHANGE COLUMN `avatar` `foto_profil` VARCHAR(255) NULL');
            }
            if (Schema::hasColumn('users', 'role')) {
                DB::statement("ALTER TABLE `users` CHANGE COLUMN `role` `peran` VARCHAR(20) NOT NULL DEFAULT 'petugas'");
            }
            if (Schema::hasColumn('users', 'study_program')) {
                DB::statement('ALTER TABLE `users` CHANGE COLUMN `study_program` `prodi_binaan` VARCHAR(150) NULL');
            }
            DB::statement('RENAME TABLE `users` TO `pengguna`');
        }

        // 3. Rename columns and table: participants -> mahasiswa
        if (Schema::hasTable('participants')) {
            if (Schema::hasColumn('participants', 'id')) {
                DB::statement('ALTER TABLE `participants` CHANGE COLUMN `id` `id_mahasiswa` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
            }
            if (Schema::hasColumn('participants', 'identifier')) {
                DB::statement("ALTER TABLE `participants` CHANGE COLUMN `identifier` `nim` VARCHAR(64) NOT NULL COMMENT 'NPM / NIM peserta apel'");
            }
            if (Schema::hasColumn('participants', 'name')) {
                DB::statement('ALTER TABLE `participants` CHANGE COLUMN `name` `nama` VARCHAR(255) NOT NULL');
            }
            if (Schema::hasColumn('participants', 'class_group')) {
                DB::statement("ALTER TABLE `participants` CHANGE COLUMN `class_group` `kelas` VARCHAR(50) NULL COMMENT 'Kompi / Pleton / Kelas'");
            }
            if (! Schema::hasColumn('participants', 'id_rfid')) {
                DB::statement('ALTER TABLE `participants` ADD COLUMN `id_rfid` BIGINT UNSIGNED NULL AFTER `kode_qr`');
            }
            DB::statement('RENAME TABLE `participants` TO `mahasiswa`');
        }

        // 4. Rename columns and table: apel_schedules -> apel
        if (Schema::hasTable('apel_schedules')) {
            if (Schema::hasColumn('apel_schedules', 'id')) {
                DB::statement('ALTER TABLE `apel_schedules` CHANGE COLUMN `id` `id_apel` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
            }
            if (Schema::hasColumn('apel_schedules', 'schedule_date')) {
                DB::statement('ALTER TABLE `apel_schedules` CHANGE COLUMN `schedule_date` `tanggal` DATE NOT NULL');
            }
            if (Schema::hasColumn('apel_schedules', 'start_time')) {
                DB::statement('ALTER TABLE `apel_schedules` CHANGE COLUMN `start_time` `jam_mulai` TIME NOT NULL');
            }
            if (Schema::hasColumn('apel_schedules', 'end_time')) {
                DB::statement('ALTER TABLE `apel_schedules` CHANGE COLUMN `end_time` `jam_selesai` TIME NOT NULL');
            }
            if (Schema::hasColumn('apel_schedules', 'category')) {
                DB::statement("ALTER TABLE `apel_schedules` CHANGE COLUMN `category` `jenis` ENUM('rutin','tambahan','khusus') NOT NULL DEFAULT 'rutin'");
            }
            DB::statement('RENAME TABLE `apel_schedules` TO `apel`');
        }

        // 5. Rename columns and table: attendance_records -> presensi
        if (Schema::hasTable('attendance_records')) {
            if (Schema::hasColumn('attendance_records', 'id')) {
                DB::statement('ALTER TABLE `attendance_records` CHANGE COLUMN `id` `id_presensi` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
            }
            if (Schema::hasColumn('attendance_records', 'apel_schedule_id')) {
                DB::statement('ALTER TABLE `attendance_records` CHANGE COLUMN `apel_schedule_id` `id_apel` BIGINT UNSIGNED NOT NULL');
            }
            if (Schema::hasColumn('attendance_records', 'participant_id')) {
                DB::statement('ALTER TABLE `attendance_records` CHANGE COLUMN `participant_id` `id_mahasiswa` BIGINT UNSIGNED NOT NULL');
            }
            if (Schema::hasColumn('attendance_records', 'check_in_time')) {
                DB::statement('ALTER TABLE `attendance_records` CHANGE COLUMN `check_in_time` `waktu_scan` TIMESTAMP NULL DEFAULT NULL');
            }
            DB::statement('RENAME TABLE `attendance_records` TO `presensi`');
        }

        // 6. Rename columns and table: permission_requests -> pengajuan_izin
        if (Schema::hasTable('permission_requests')) {
            if (Schema::hasColumn('permission_requests', 'id')) {
                DB::statement('ALTER TABLE `permission_requests` CHANGE COLUMN `id` `id_izin` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
            }
            if (Schema::hasColumn('permission_requests', 'participant_id')) {
                DB::statement('ALTER TABLE `permission_requests` CHANGE COLUMN `participant_id` `id_mahasiswa` BIGINT UNSIGNED NOT NULL');
            }
            if (Schema::hasColumn('permission_requests', 'apel_schedule_id')) {
                DB::statement('ALTER TABLE `permission_requests` CHANGE COLUMN `apel_schedule_id` `id_apel` BIGINT UNSIGNED NULL DEFAULT NULL');
            }
            if (Schema::hasColumn('permission_requests', 'decided_by')) {
                DB::statement('ALTER TABLE `permission_requests` CHANGE COLUMN `decided_by` `id_timdis` BIGINT UNSIGNED NULL DEFAULT NULL');
            }
            if (Schema::hasColumn('permission_requests', 'type')) {
                DB::statement("ALTER TABLE `permission_requests` CHANGE COLUMN `type` `jenis` ENUM('izin','sakit') NOT NULL DEFAULT 'izin'");
            }
            if (Schema::hasColumn('permission_requests', 'reason')) {
                DB::statement('ALTER TABLE `permission_requests` CHANGE COLUMN `reason` `alasan` TEXT NOT NULL');
            }
            if (Schema::hasColumn('permission_requests', 'attachment_path')) {
                DB::statement('ALTER TABLE `permission_requests` CHANGE COLUMN `attachment_path` `berkas_bukti` VARCHAR(255) NULL DEFAULT NULL');
            }
            DB::statement('RENAME TABLE `permission_requests` TO `pengajuan_izin`');
        }

        // 7. Rename columns and table: ekuliah_sync_logs -> log_sinkronisasi
        if (Schema::hasTable('ekuliah_sync_logs')) {
            if (Schema::hasColumn('ekuliah_sync_logs', 'id')) {
                DB::statement('ALTER TABLE `ekuliah_sync_logs` CHANGE COLUMN `id` `id_sinkron` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
            }
            if (Schema::hasColumn('ekuliah_sync_logs', 'records_synced')) {
                DB::statement('ALTER TABLE `ekuliah_sync_logs` CHANGE COLUMN `records_synced` `jumlah_data` INT UNSIGNED NOT NULL DEFAULT 0');
            }
            if (Schema::hasColumn('ekuliah_sync_logs', 'synced_by')) {
                DB::statement('ALTER TABLE `ekuliah_sync_logs` CHANGE COLUMN `synced_by` `id_admin` BIGINT UNSIGNED NULL DEFAULT NULL');
            }
            if (! Schema::hasColumn('ekuliah_sync_logs', 'waktu')) {
                DB::statement('ALTER TABLE `ekuliah_sync_logs` ADD COLUMN `waktu` DATETIME NULL AFTER `id_admin`');
                DB::statement('UPDATE `ekuliah_sync_logs` SET `waktu` = `created_at`');
            }
            DB::statement('RENAME TABLE `ekuliah_sync_logs` TO `log_sinkronisasi`');
        }

        // 8. Update FK column names in other related tables
        if (Schema::hasTable('attendance_events')) {
            if (Schema::hasColumn('attendance_events', 'apel_schedule_id')) {
                DB::statement('ALTER TABLE `attendance_events` CHANGE COLUMN `apel_schedule_id` `id_apel` BIGINT UNSIGNED NULL DEFAULT NULL');
            }
            if (Schema::hasColumn('attendance_events', 'participant_id')) {
                DB::statement('ALTER TABLE `attendance_events` CHANGE COLUMN `participant_id` `id_mahasiswa` BIGINT UNSIGNED NULL DEFAULT NULL');
            }
        }

        if (Schema::hasTable('rfid_cards')) {
            if (Schema::hasColumn('rfid_cards', 'participant_id')) {
                DB::statement('ALTER TABLE `rfid_cards` CHANGE COLUMN `participant_id` `id_mahasiswa` BIGINT UNSIGNED NOT NULL');
            }
        }

        if (Schema::hasTable('audit_logs')) {
            if (Schema::hasColumn('audit_logs', 'user_id')) {
                DB::statement('ALTER TABLE `audit_logs` CHANGE COLUMN `user_id` `id_pengguna` BIGINT UNSIGNED NULL DEFAULT NULL');
            }
        }

        // 9. Re-add foreign keys pointing to new tables & columns
        if (Schema::hasTable('apel') && Schema::hasTable('pengguna')) {
            DB::statement('ALTER TABLE `apel` ADD CONSTRAINT `apel_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE SET NULL');
        }

        if (Schema::hasTable('presensi')) {
            if (Schema::hasTable('apel')) {
                DB::statement('ALTER TABLE `presensi` ADD CONSTRAINT `presensi_id_apel_foreign` FOREIGN KEY (`id_apel`) REFERENCES `apel` (`id_apel`) ON DELETE CASCADE');
            }
            if (Schema::hasTable('mahasiswa')) {
                DB::statement('ALTER TABLE `presensi` ADD CONSTRAINT `presensi_id_mahasiswa_foreign` FOREIGN KEY (`id_mahasiswa`) REFERENCES `mahasiswa` (`id_mahasiswa`) ON DELETE CASCADE');
            }
            if (Schema::hasTable('pengguna')) {
                DB::statement('ALTER TABLE `presensi` ADD CONSTRAINT `presensi_recorded_by_foreign` FOREIGN KEY (`recorded_by`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE SET NULL');
            }
            if (Schema::hasTable('rfid_cards')) {
                DB::statement('ALTER TABLE `presensi` ADD CONSTRAINT `presensi_rfid_card_id_foreign` FOREIGN KEY (`rfid_card_id`) REFERENCES `rfid_cards` (`id`) ON DELETE SET NULL');
            }
        }

        if (Schema::hasTable('pengajuan_izin')) {
            if (Schema::hasTable('apel')) {
                DB::statement('ALTER TABLE `pengajuan_izin` ADD CONSTRAINT `pengajuan_izin_id_apel_foreign` FOREIGN KEY (`id_apel`) REFERENCES `apel` (`id_apel`) ON DELETE SET NULL');
            }
            if (Schema::hasTable('mahasiswa')) {
                DB::statement('ALTER TABLE `pengajuan_izin` ADD CONSTRAINT `pengajuan_izin_id_mahasiswa_foreign` FOREIGN KEY (`id_mahasiswa`) REFERENCES `mahasiswa` (`id_mahasiswa`) ON DELETE CASCADE');
            }
            if (Schema::hasTable('pengguna')) {
                DB::statement('ALTER TABLE `pengajuan_izin` ADD CONSTRAINT `pengajuan_izin_id_timdis_foreign` FOREIGN KEY (`id_timdis`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE SET NULL');
            }
        }

        if (Schema::hasTable('log_sinkronisasi') && Schema::hasTable('pengguna')) {
            DB::statement('ALTER TABLE `log_sinkronisasi` ADD CONSTRAINT `log_sinkronisasi_id_admin_foreign` FOREIGN KEY (`id_admin`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE SET NULL');
        }

        if (Schema::hasTable('attendance_events')) {
            if (Schema::hasTable('apel')) {
                DB::statement('ALTER TABLE `attendance_events` ADD CONSTRAINT `attendance_events_id_apel_foreign` FOREIGN KEY (`id_apel`) REFERENCES `apel` (`id_apel`) ON DELETE CASCADE');
            }
            if (Schema::hasTable('mahasiswa')) {
                DB::statement('ALTER TABLE `attendance_events` ADD CONSTRAINT `attendance_events_id_mahasiswa_foreign` FOREIGN KEY (`id_mahasiswa`) REFERENCES `mahasiswa` (`id_mahasiswa`) ON DELETE CASCADE');
            }
        }

        if (Schema::hasTable('rfid_cards') && Schema::hasTable('mahasiswa')) {
            DB::statement('ALTER TABLE `rfid_cards` ADD CONSTRAINT `rfid_cards_id_mahasiswa_foreign` FOREIGN KEY (`id_mahasiswa`) REFERENCES `mahasiswa` (`id_mahasiswa`) ON DELETE CASCADE');
        }

        if (Schema::hasTable('audit_logs') && Schema::hasTable('pengguna')) {
            DB::statement('ALTER TABLE `audit_logs` ADD CONSTRAINT `audit_logs_id_pengguna_foreign` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE SET NULL');
        }

        // Populate id_rfid on mahasiswa if active rfid_card exists
        if (Schema::hasTable('mahasiswa') && Schema::hasTable('rfid_cards') && Schema::hasColumn('mahasiswa', 'id_rfid')) {
            DB::statement('UPDATE `mahasiswa` m JOIN `rfid_cards` r ON r.id_mahasiswa = m.id_mahasiswa AND r.is_active = 1 SET m.id_rfid = r.id');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop new foreign keys safely
        $foreignKeysToDrop = [
            ['table' => 'apel', 'fk' => 'apel_created_by_foreign'],
            ['table' => 'presensi', 'fk' => 'presensi_id_apel_foreign'],
            ['table' => 'presensi', 'fk' => 'presensi_id_mahasiswa_foreign'],
            ['table' => 'presensi', 'fk' => 'presensi_recorded_by_foreign'],
            ['table' => 'presensi', 'fk' => 'presensi_rfid_card_id_foreign'],
            ['table' => 'pengajuan_izin', 'fk' => 'pengajuan_izin_id_apel_foreign'],
            ['table' => 'pengajuan_izin', 'fk' => 'pengajuan_izin_id_mahasiswa_foreign'],
            ['table' => 'pengajuan_izin', 'fk' => 'pengajuan_izin_id_timdis_foreign'],
            ['table' => 'log_sinkronisasi', 'fk' => 'log_sinkronisasi_id_admin_foreign'],
            ['table' => 'attendance_events', 'fk' => 'attendance_events_id_apel_foreign'],
            ['table' => 'attendance_events', 'fk' => 'attendance_events_id_mahasiswa_foreign'],
            ['table' => 'rfid_cards', 'fk' => 'rfid_cards_id_mahasiswa_foreign'],
            ['table' => 'audit_logs', 'fk' => 'audit_logs_id_pengguna_foreign'],
        ];

        foreach ($foreignKeysToDrop as $item) {
            if (Schema::hasTable($item['table'])) {
                DB::statement("ALTER TABLE `{$item['table']}` DROP CONSTRAINT IF EXISTS `{$item['fk']}`");
            }
        }

        // Revert related table columns
        if (Schema::hasTable('audit_logs') && Schema::hasColumn('audit_logs', 'id_pengguna')) {
            DB::statement('ALTER TABLE `audit_logs` CHANGE COLUMN `id_pengguna` `user_id` BIGINT UNSIGNED NULL DEFAULT NULL');
        }

        if (Schema::hasTable('rfid_cards') && Schema::hasColumn('rfid_cards', 'id_mahasiswa')) {
            DB::statement('ALTER TABLE `rfid_cards` CHANGE COLUMN `id_mahasiswa` `participant_id` BIGINT UNSIGNED NOT NULL');
        }

        if (Schema::hasTable('attendance_events')) {
            if (Schema::hasColumn('attendance_events', 'id_apel')) {
                DB::statement('ALTER TABLE `attendance_events` CHANGE COLUMN `id_apel` `apel_schedule_id` BIGINT UNSIGNED NULL DEFAULT NULL');
            }
            if (Schema::hasColumn('attendance_events', 'id_mahasiswa')) {
                DB::statement('ALTER TABLE `attendance_events` CHANGE COLUMN `id_mahasiswa` `participant_id` BIGINT UNSIGNED NULL DEFAULT NULL');
            }
        }

        // Revert log_sinkronisasi -> ekuliah_sync_logs
        if (Schema::hasTable('log_sinkronisasi')) {
            DB::statement('RENAME TABLE `log_sinkronisasi` TO `ekuliah_sync_logs`');
            if (Schema::hasColumn('ekuliah_sync_logs', 'id_sinkron')) {
                DB::statement('ALTER TABLE `ekuliah_sync_logs` CHANGE COLUMN `id_sinkron` `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
            }
            if (Schema::hasColumn('ekuliah_sync_logs', 'jumlah_data')) {
                DB::statement('ALTER TABLE `ekuliah_sync_logs` CHANGE COLUMN `jumlah_data` `records_synced` INT UNSIGNED NOT NULL DEFAULT 0');
            }
            if (Schema::hasColumn('ekuliah_sync_logs', 'id_admin')) {
                DB::statement('ALTER TABLE `ekuliah_sync_logs` CHANGE COLUMN `id_admin` `synced_by` BIGINT UNSIGNED NULL DEFAULT NULL');
            }
        }

        // Revert pengajuan_izin -> permission_requests
        if (Schema::hasTable('pengajuan_izin')) {
            DB::statement('RENAME TABLE `pengajuan_izin` TO `permission_requests`');
            if (Schema::hasColumn('permission_requests', 'id_izin')) {
                DB::statement('ALTER TABLE `permission_requests` CHANGE COLUMN `id_izin` `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
            }
            if (Schema::hasColumn('permission_requests', 'id_mahasiswa')) {
                DB::statement('ALTER TABLE `permission_requests` CHANGE COLUMN `id_mahasiswa` `participant_id` BIGINT UNSIGNED NOT NULL');
            }
            if (Schema::hasColumn('permission_requests', 'id_apel')) {
                DB::statement('ALTER TABLE `permission_requests` CHANGE COLUMN `id_apel` `apel_schedule_id` BIGINT UNSIGNED NULL DEFAULT NULL');
            }
            if (Schema::hasColumn('permission_requests', 'id_timdis')) {
                DB::statement('ALTER TABLE `permission_requests` CHANGE COLUMN `id_timdis` `decided_by` BIGINT UNSIGNED NULL DEFAULT NULL');
            }
            if (Schema::hasColumn('permission_requests', 'jenis')) {
                DB::statement("ALTER TABLE `permission_requests` CHANGE COLUMN `jenis` `type` ENUM('izin','sakit') NOT NULL DEFAULT 'izin'");
            }
            if (Schema::hasColumn('permission_requests', 'alasan')) {
                DB::statement('ALTER TABLE `permission_requests` CHANGE COLUMN `alasan` `reason` TEXT NOT NULL');
            }
            if (Schema::hasColumn('permission_requests', 'berkas_bukti')) {
                DB::statement('ALTER TABLE `permission_requests` CHANGE COLUMN `berkas_bukti` `attachment_path` VARCHAR(255) NULL DEFAULT NULL');
            }
        }

        // Revert presensi -> attendance_records
        if (Schema::hasTable('presensi')) {
            DB::statement('RENAME TABLE `presensi` TO `attendance_records`');
            if (Schema::hasColumn('attendance_records', 'id_presensi')) {
                DB::statement('ALTER TABLE `attendance_records` CHANGE COLUMN `id_presensi` `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
            }
            if (Schema::hasColumn('attendance_records', 'id_apel')) {
                DB::statement('ALTER TABLE `attendance_records` CHANGE COLUMN `id_apel` `apel_schedule_id` BIGINT UNSIGNED NOT NULL');
            }
            if (Schema::hasColumn('attendance_records', 'id_mahasiswa')) {
                DB::statement('ALTER TABLE `attendance_records` CHANGE COLUMN `id_mahasiswa` `participant_id` BIGINT UNSIGNED NOT NULL');
            }
            if (Schema::hasColumn('attendance_records', 'waktu_scan')) {
                DB::statement('ALTER TABLE `attendance_records` CHANGE COLUMN `waktu_scan` `check_in_time` TIMESTAMP NULL DEFAULT NULL');
            }
        }

        // Revert apel -> apel_schedules
        if (Schema::hasTable('apel')) {
            DB::statement('RENAME TABLE `apel` TO `apel_schedules`');
            if (Schema::hasColumn('apel_schedules', 'id_apel')) {
                DB::statement('ALTER TABLE `apel_schedules` CHANGE COLUMN `id_apel` `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
            }
            if (Schema::hasColumn('apel_schedules', 'tanggal')) {
                DB::statement('ALTER TABLE `apel_schedules` CHANGE COLUMN `tanggal` `schedule_date` DATE NOT NULL');
            }
            if (Schema::hasColumn('apel_schedules', 'jam_mulai')) {
                DB::statement('ALTER TABLE `apel_schedules` CHANGE COLUMN `jam_mulai` `start_time` TIME NOT NULL');
            }
            if (Schema::hasColumn('apel_schedules', 'jam_selesai')) {
                DB::statement('ALTER TABLE `apel_schedules` CHANGE COLUMN `jam_selesai` `end_time` TIME NOT NULL');
            }
            if (Schema::hasColumn('apel_schedules', 'jenis')) {
                DB::statement("ALTER TABLE `apel_schedules` CHANGE COLUMN `jenis` `category` ENUM('rutin','tambahan','khusus') NOT NULL DEFAULT 'rutin'");
            }
        }

        // Revert mahasiswa -> participants
        if (Schema::hasTable('mahasiswa')) {
            DB::statement('RENAME TABLE `mahasiswa` TO `participants`');
            if (Schema::hasColumn('participants', 'id_mahasiswa')) {
                DB::statement('ALTER TABLE `participants` CHANGE COLUMN `id_mahasiswa` `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
            }
            if (Schema::hasColumn('participants', 'nim') || Schema::hasColumn('participants', 'npm')) {
                $col = Schema::hasColumn('participants', 'npm') ? 'npm' : 'nim';
                DB::statement("ALTER TABLE `participants` CHANGE COLUMN `{$col}` `identifier` VARCHAR(64) NOT NULL COMMENT 'NPM / NIM peserta apel'");
            }
            if (Schema::hasColumn('participants', 'nama')) {
                DB::statement('ALTER TABLE `participants` CHANGE COLUMN `nama` `name` VARCHAR(255) NOT NULL');
            }
            if (Schema::hasColumn('participants', 'kelas')) {
                DB::statement("ALTER TABLE `participants` CHANGE COLUMN `kelas` `class_group` VARCHAR(50) NULL COMMENT 'Kompi / Pleton / Kelas'");
            }
        }

        // Revert pengguna -> users
        if (Schema::hasTable('pengguna')) {
            DB::statement('RENAME TABLE `pengguna` TO `users`');
            if (Schema::hasColumn('users', 'id_pengguna')) {
                DB::statement('ALTER TABLE `users` CHANGE COLUMN `id_pengguna` `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
            }
            if (Schema::hasColumn('users', 'nama')) {
                DB::statement('ALTER TABLE `users` CHANGE COLUMN `nama` `name` VARCHAR(255) NOT NULL');
            }
            if (Schema::hasColumn('users', 'kata_sandi')) {
                DB::statement('ALTER TABLE `users` CHANGE COLUMN `kata_sandi` `password` VARCHAR(255) NOT NULL');
            }
            if (Schema::hasColumn('users', 'foto_profil')) {
                DB::statement('ALTER TABLE `users` CHANGE COLUMN `foto_profil` `avatar` VARCHAR(255) NULL');
            }
            if (Schema::hasColumn('users', 'peran')) {
                DB::statement("ALTER TABLE `users` CHANGE COLUMN `peran` `role` VARCHAR(20) NOT NULL DEFAULT 'petugas'");
            }
            if (Schema::hasColumn('users', 'prodi_binaan')) {
                DB::statement('ALTER TABLE `users` CHANGE COLUMN `prodi_binaan` `study_program` VARCHAR(150) NULL');
            }
        }
    }
};
