<?php

namespace Database\Seeders;

use App\Models\ApelSchedule;
use App\Models\Participant;
use App\Models\PermissionRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PermissionRequestSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $adminId = $admin?->id ?? 1;

        $timdisTrpl = User::where('study_program', 'Teknologi Rekayasa Perangkat Lunak')->first()
            ?? User::where('role', 'timdis')->first();

        // Cari peserta TRPL
        $participantsTrpl = Participant::where('study_program', 'Teknologi Rekayasa Perangkat Lunak')
            ->take(12)
            ->get();

        if ($participantsTrpl->isEmpty()) {
            $participantsTrpl = Participant::take(12)->get();
        }

        $schedules = ApelSchedule::orderBy('schedule_date', 'desc')->take(5)->get();
        $latestSchedule = $schedules->first();

        $dummyList = [
            [
                'name' => 'Yoga Saputra',
                'type' => 'sakit',
                'reason' => 'Demam tinggi dan flu berat, istirahat atas rekomendasi dokter klinik.',
                'status' => 'menunggu',
                'created_at' => Carbon::parse('2026-10-20 16:30:00'),
                'attachment' => 'surat_dokter_yoga.pdf',
            ],
            [
                'name' => 'Dewi Lestari',
                'type' => 'sakit',
                'reason' => 'Sakit migrain parah dan vertigo.',
                'status' => 'menunggu',
                'created_at' => Carbon::parse('2026-10-20 15:12:00'),
                'attachment' => 'surat_klinik_dewi.pdf',
            ],
            [
                'name' => 'Rina Kusuma',
                'type' => 'sakit',
                'reason' => 'Infeksi saluran pencernaan (gastroenteritis).',
                'status' => 'menunggu',
                'created_at' => Carbon::parse('2026-10-20 14:05:00'),
                'attachment' => 'surat_sakit_rina.pdf',
            ],
            [
                'name' => 'Maya Permata',
                'type' => 'sakit',
                'reason' => 'Cedera pergelangan kaki terkilir saat olahraga.',
                'status' => 'menunggu',
                'created_at' => Carbon::parse('2026-10-20 11:20:00'),
                'attachment' => 'surat_rs_maya.pdf',
            ],
            [
                'name' => 'Dimas Pratama',
                'type' => 'sakit',
                'reason' => 'Pasca tindakan pencabutan gigi bungsu impaksi.',
                'status' => 'menunggu',
                'created_at' => Carbon::parse('2026-10-19 13:40:00'),
                'attachment' => 'surat_dokter_dimas.pdf',
            ],
            [
                'name' => 'Budi Setiawan',
                'type' => 'izin',
                'reason' => 'Mewakili kampus dalam kompetisi Hackathon tingkat nasional.',
                'status' => 'menunggu',
                'created_at' => Carbon::parse('2026-10-19 09:15:00'),
                'attachment' => 'surat_tugas_budi.pdf',
            ],
            [
                'name' => 'Fajar Nugraha',
                'type' => 'izin',
                'reason' => 'Acara keluarga mendesak dan kedukaan keluarga kandung.',
                'status' => 'menunggu',
                'created_at' => Carbon::parse('2026-10-18 20:10:00'),
                'attachment' => 'surat_keterangan_fajar.pdf',
            ],
            [
                'name' => 'Intan Permatasari',
                'type' => 'sakit',
                'reason' => 'Demam berdarah dan perawatan intensif di rumah sakit.',
                'status' => 'menunggu',
                'created_at' => Carbon::parse('2026-10-18 17:35:00'),
                'attachment' => 'surat_lab_intan.pdf',
            ],
            // Data Disetujui
            [
                'name' => 'Ahmad Fauzi Rahman',
                'type' => 'sakit',
                'reason' => 'Demam dan radang tenggorokan akut.',
                'status' => 'disetujui',
                'created_at' => Carbon::parse('2026-10-15 08:20:00'),
                'attachment' => 'surat_dokter_fauzi.pdf',
                'decided_by' => $timdisTrpl?->id ?? $adminId,
                'decided_at' => Carbon::parse('2026-10-15 09:00:00'),
                'notes' => 'Disetujui, istirahat semoga lekas sembuh.',
            ],
            [
                'name' => 'Annisa Nur Fadhilah',
                'type' => 'izin',
                'reason' => 'Menghadiri seleksi wawancara beasiswa luar negeri.',
                'status' => 'disetujui',
                'created_at' => Carbon::parse('2026-10-12 10:15:00'),
                'attachment' => 'undangan_beasiswa_annisa.pdf',
                'decided_by' => $timdisTrpl?->id ?? $adminId,
                'decided_at' => Carbon::parse('2026-10-12 11:30:00'),
                'notes' => 'Disetujui dispensasi akademik.',
            ],
            // Data Ditolak
            [
                'name' => 'Arya Bagus Maulana',
                'type' => 'izin',
                'reason' => 'Bangun kesiangan dan kendaraan macet.',
                'status' => 'ditolak',
                'created_at' => Carbon::parse('2026-10-10 07:45:00'),
                'attachment' => null,
                'decided_by' => $timdisTrpl?->id ?? $adminId,
                'decided_at' => Carbon::parse('2026-10-10 08:15:00'),
                'notes' => 'Ditolak: Alasan tidak memenuhi kriteria dispensasi izin.',
            ],
        ];

        foreach ($dummyList as $idx => $item) {
            $p = $participantsTrpl->get($idx % $participantsTrpl->count());
            if (! $p) {
                continue;
            }

            PermissionRequest::create([
                'participant_id' => $p->id,
                'apel_schedule_id' => $latestSchedule?->id,
                'type' => $item['type'],
                'reason' => $item['reason'],
                'attachment_path' => $item['attachment'] ?? null,
                'status' => $item['status'],
                'decided_by' => $item['decided_by'] ?? null,
                'decided_at' => $item['decided_at'] ?? null,
                'notes' => $item['notes'] ?? null,
                'created_at' => $item['created_at'],
                'updated_at' => $item['created_at'],
            ]);
        }
    }
}
