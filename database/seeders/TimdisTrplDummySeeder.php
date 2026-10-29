<?php

namespace Database\Seeders;

use App\Models\ApelSchedule;
use App\Models\AttendanceRecord;
use App\Models\Participant;
use App\Models\RfidCard;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TimdisTrplDummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Dapatkan akun Timdis TRPL (Budi Santoso)
        $timdisTrpl = User::where('email', 'budi.timdis@polinela.ac.id')->first()
            ?? User::where('study_program', 'Teknologi Rekayasa Perangkat Lunak')->first()
            ?? User::where('role', 'timdis')->first();

        $targetClass = $timdisTrpl?->class_group ?? 'TRPL 1B';
        $targetCohort = $timdisTrpl?->cohort_year ?? '2025';

        // 2. Daftar 50 nama mahasiswa TRPL Indonesia
        $names = [
            'Aditya Pratama Nugraha',
            'Ahmad Fauzi Rahman',
            'Alfin Dwi Cahyono',
            'Ananda Rizky Ramadhan',
            'Andhika Bayu Saputra',
            'Angga Wahyu Pratama',
            'Annisa Nur Fadhilah',
            'Arief Tri Wibowo',
            'Arya Bagus Maulana',
            'Aulia Rahma Putri',
            'Bagas Christian Wijaya',
            'Bella Safitri',
            'Bima Arya Pratama',
            'Candra Kusuma Wardana',
            'Chelsea Olivia Putri',
            'Danang Eko Prasetyo',
            'Dhea Ayu Lestari',
            'Dimas Aldi Firmansyah',
            'Dinda Permata Sari',
            'Doni Setiawan',
            'Fadhil Muhammad Iqbal',
            'Fajar Ramadhan',
            'Farhan Ardiansyah',
            'Febri Rahmawati',
            'Fikri Haikal Pratama',
            'Galih Permadi',
            'Gita Cahya Ningrum',
            'Hafidz Nur Syahputra',
            'Ilham Maulana Akbar',
            'Indah Permatasari',
            'Ivan Kurniawan',
            'Joko Susilo Utomo',
            'Kevin Marcelino',
            'Larasati Dewi',
            'M. Daffa Al-Ghifari',
            'M. Farhan Al-Fathan',
            'M. Rafi Ramadhan',
            'Nabila Khairunnisa',
            'Naufal Zaki Nugraha',
            'Putra Eka Wardana',
            'Rahmat Hidayatullah',
            'Reza Pahlevi',
            'Rian Adriansyah',
            'Ridho Firmansyah',
            'Rizky Fadillah',
            'Satria Bintang Timur',
            'Septian Dwi Nugroho',
            'Siti Nurhaliza Putri',
            'Tegar Prasetyo',
            'Wahyu Aji Pratama',
        ];

        // 3. Jadwal Apel untuk rekaman absensi
        // Jadwal aktif (2026-10-28)
        $schedule = ApelSchedule::where('status', 'active')->first()
            ?? ApelSchedule::whereDate('schedule_date', '2026-10-28')->first()
            ?? ApelSchedule::latest('schedule_date')->first();

        $scheduleDate = $schedule ? Carbon::parse($schedule->schedule_date)->format('Y-m-d') : '2026-10-28';

        $createdParticipants = [];

        foreach ($names as $idx => $name) {
            $npmNumber = 22753050 + $idx;
            $identifier = (string) $npmNumber;

            // Samakan class_group dan cohort_year dengan profil binaan Timdis TRPL
            $participant = Participant::updateOrCreate(
                ['identifier' => $identifier],
                [
                    'name' => $name,
                    'department' => 'Teknologi Informasi',
                    'study_program' => 'Teknologi Rekayasa Perangkat Lunak',
                    'class_group' => $targetClass,
                    'cohort_year' => $targetCohort,
                    'is_active' => true,
                ]
            );

            // RFID Card
            $cardUid = 'TRPL_' . strtoupper(Str::random(8));
            RfidCard::firstOrCreate(
                ['participant_id' => $participant->id],
                [
                    'card_uid' => $cardUid,
                    'is_active' => true,
                    'issued_at' => now()->subMonths(3),
                ]
            );

            $createdParticipants[] = $participant;
        }

        // 4. Buat data AttendanceRecord untuk 50 mahasiswa ini
        // Distribusi: 36 Hadir tepat waktu, 6 Terlambat, 4 Sakit/Izin, 4 Tanpa Keterangan
        foreach ($createdParticipants as $idx => $p) {
            if ($idx < 36) {
                // Hadir tepat (06:35 - 06:58)
                $minute = str_pad(35 + ($idx % 24), 2, '0', STR_PAD_LEFT);
                $status = 'hadir';
                $checkIn = Carbon::parse("{$scheduleDate} 06:{$minute}:00");
            } elseif ($idx < 42) {
                // Terlambat (07:16 - 07:28)
                $minute = str_pad(16 + (($idx - 36) * 2), 2, '0', STR_PAD_LEFT);
                $status = 'terlambat';
                $checkIn = Carbon::parse("{$scheduleDate} 07:{$minute}:00");
            } elseif ($idx < 46) {
                // Sakit / Izin
                $status = ($idx % 2 === 0) ? 'sakit' : 'izin';
                $checkIn = null;
            } else {
                // Tanpa Keterangan / Alpa
                $status = 'alpa';
                $checkIn = null;
            }

            if ($schedule) {
                AttendanceRecord::updateOrCreate(
                    [
                        'participant_id' => $p->id,
                        'apel_schedule_id' => $schedule->id,
                    ],
                    [
                        'rfid_card_id' => $p->activeRfidCard?->id,
                        'status' => $status,
                        'check_in_time' => $checkIn,
                        'recorded_by' => $timdisTrpl?->id ?? 1,
                        'device_identifier' => 'SCANNER_TRPL_GATE',
                        'notes' => $status === 'sakit' ? 'Surat dokter terlampir' : ($status === 'izin' ? 'Dispensasi lomba' : null),
                    ]
                );
            }
        }
    }
}
