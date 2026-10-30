<?php

namespace Database\Seeders;

use App\Models\ApelSchedule;
use App\Models\EkuliahSyncLog;
use App\Models\Participant;
use App\Models\PermissionRequest;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Database\Seeder;

class IaFeaturesSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Settings E-Kuliah
        SystemSetting::set('ekuliah_url', 'https://ekuliah.polinela.ac.id/api/v1', 'integrasi');
        SystemSetting::set('ekuliah_api_key', 'polinela_live_sec_89df7182a9', 'integrasi');
        SystemSetting::set('ekuliah_auto_sync', '1', 'integrasi');
        SystemSetting::set('ekuliah_last_synced', now()->subHours(3)->toDateTimeString(), 'integrasi');

        // 2. Update Admin & Timdis profile fields
        $admin = User::where('role', 'admin')->first();
        if ($admin) {
            $admin->update([
                'study_program' => 'Pusat Komputer & TI',
                'phone' => '081278990011',
            ]);
        }

        $timdis1 = User::where('username', 'timdis1')->first();
        if ($timdis1) {
            $timdis1->update([
                'study_program' => 'Manajemen Informatika',
                'phone' => '081399882233',
            ]);
        }

        // 3. Update categories for apel schedules
        ApelSchedule::where('title', 'like', '%Pagi%')->update(['category' => 'rutin']);
        ApelSchedule::where('title', 'like', '%Tambahan%')->update(['category' => 'tambahan']);
        ApelSchedule::where('title', 'like', '%Khusus%')->update(['category' => 'khusus']);

        // 4. Sample Permission Requests (Izin & Sakit)
        $schedule = ApelSchedule::latest('schedule_date')->first();
        $participants = Participant::take(8)->get();
        $timdisUsers = User::whereIn('role', ['timdis', 'petugas'])->get();
        $decider = $timdisUsers->first() ?? $admin;

        if ($participants->count() >= 3) {
            // Pending Requests
            PermissionRequest::firstOrCreate([
                'participant_id' => $participants[0]->id,
                'apel_schedule_id' => $schedule?->id,
            ], [
                'type' => 'sakit',
                'reason' => 'Demam tinggi dan flu berat, istirahat di kos sesuai anjuran dokter puskesmas.',
                'attachment_path' => 'surat_keterangan_dokter_puskesmas.pdf',
                'status' => 'menunggu',
                'notes' => null,
            ]);

            PermissionRequest::firstOrCreate([
                'participant_id' => $participants[1]->id,
                'apel_schedule_id' => $schedule?->id,
            ], [
                'type' => 'izin',
                'reason' => 'Mengikuti Lomba Inovasi Nasional mewakili Politeknik Negeri Lampung di Universitas Indonesia.',
                'attachment_path' => 'surat_tugas_lomba_kemahasiswaan.pdf',
                'status' => 'menunggu',
                'notes' => null,
            ]);

            if (isset($participants[2])) {
                PermissionRequest::firstOrCreate([
                    'participant_id' => $participants[2]->id,
                    'apel_schedule_id' => $schedule?->id,
                ], [
                    'type' => 'izin',
                    'reason' => 'Ada musibah keluarga di kampung halaman, izin meninggalkan kampus 2 hari.',
                    'attachment_path' => 'surat_izin_orang_tua.pdf',
                    'status' => 'menunggu',
                    'notes' => null,
                ]);
            }

            // Approved Request
            if (isset($participants[3])) {
                PermissionRequest::firstOrCreate([
                    'participant_id' => $participants[3]->id,
                    'apel_schedule_id' => $schedule?->id,
                ], [
                    'type' => 'sakit',
                    'reason' => 'Cedera engkel saat latihan olahraga kemahasiswaan.',
                    'attachment_path' => 'hasil_rontgen_rsud.pdf',
                    'status' => 'disetujui',
                    'decided_by' => $decider?->id,
                    'decided_at' => now()->subHours(2),
                    'notes' => 'Surat dan bukti valid, disetujui Timdis Dwi Lestari.',
                ]);
            }

            // Rejected Request
            if (isset($participants[4])) {
                PermissionRequest::firstOrCreate([
                    'participant_id' => $participants[4]->id,
                    'apel_schedule_id' => $schedule?->id,
                ], [
                    'type' => 'izin',
                    'reason' => 'Bangun kesiangan karena lembur mengerjakan tugas.',
                    'attachment_path' => null,
                    'status' => 'ditolak',
                    'decided_by' => $decider?->id,
                    'decided_at' => now()->subHours(1),
                    'notes' => 'Bukan alasan yang sah untuk tidak mengikuti apel kedisiplinan.',
                ]);
            }
        }

        // 5. Initial Sync Logs E-Kuliah
        EkuliahSyncLog::firstOrCreate([
            'sync_type' => 'mahasiswa',
            'status' => 'success',
            'records_synced' => 1200,
            'message' => 'Sinkronisasi master data mahasiswa dari E-Kuliah berhasil.',
            'details' => ['synced_prodi' => 8, 'updated' => 45, 'inserted' => 12],
            'synced_by' => $admin?->id,
            'created_at' => now()->subHours(5),
        ]);

        EkuliahSyncLog::firstOrCreate([
            'sync_type' => 'kehadiran',
            'status' => 'success',
            'records_synced' => 312,
            'message' => 'Pengiriman data presensi apel sesi pagi ke sistem akademik E-Kuliah berhasil.',
            'details' => ['apel_id' => $schedule?->id, 'session' => $schedule?->title],
            'synced_by' => $admin?->id,
            'created_at' => now()->subHours(3),
        ]);
    }
}
