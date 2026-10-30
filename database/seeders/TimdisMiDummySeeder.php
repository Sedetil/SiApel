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

class TimdisMiDummySeeder extends Seeder
{
    public function run(): void
    {
        $timdisMi = User::where('email', 'hendro.timdis@polinela.ac.id')->first();
        if (! $timdisMi) {
            return;
        }

        $names = [
            'Bagas Aditya Pratama',
            'Budi Setiawan',
            'Citra Kirana Sari',
            'Dendi Rahman Hakim',
            'Eka Saputri',
            'Farhan Maulana',
            'Gilang Ramadhan',
            'Hani Nuraini',
            'Irfan Hakim',
            'Jasmine Aulia',
            'Kurnia Mega Putri',
            'Lukman Hakim',
            'Maya Indah Permata',
            'Nanda Pratama',
            'Oki Setiawan',
            'Panji Asmoro Bangun',
            'Qori Agustina',
            'Rifqi Ahmad Zaki',
            'Salsabila Putri',
            'Taufik Hidayat',
            'Umar Faruq',
            'Vina Panduwinata',
            'Wildan Mukhallad',
            'Yoga Pratama',
            'Zahra Amalia',
        ];

        $schedule = ApelSchedule::where('status', 'active')->first()
            ?? ApelSchedule::whereDate('schedule_date', '2026-10-28')->first()
            ?? ApelSchedule::latest('schedule_date')->first();

        $scheduleDate = $schedule ? Carbon::parse($schedule->schedule_date)->format('Y-m-d') : '2026-10-28';

        foreach ($names as $idx => $name) {
            $identifier = (string) (24753010 + $idx);
            $participant = Participant::updateOrCreate(
                ['identifier' => $identifier],
                [
                    'name' => $name,
                    'department' => 'Teknologi Informasi',
                    'study_program' => 'Manajemen Informatika',
                    'class_group' => 'MI 3A',
                    'cohort_year' => '2024',
                    'is_active' => true,
                ]
            );

            $cardUid = 'MI_'.strtoupper(Str::random(8));
            RfidCard::firstOrCreate(
                ['participant_id' => $participant->id],
                [
                    'card_uid' => $cardUid,
                    'is_active' => true,
                    'issued_at' => now()->subMonths(4),
                ]
            );

            if ($schedule) {
                if ($idx < 18) {
                    $minute = str_pad(35 + ($idx % 20), 2, '0', STR_PAD_LEFT);
                    $status = 'hadir';
                    $checkIn = Carbon::parse("{$scheduleDate} 06:{$minute}:00");
                } elseif ($idx < 21) {
                    $minute = str_pad(18 + ($idx % 5), 2, '0', STR_PAD_LEFT);
                    $status = 'terlambat';
                    $checkIn = Carbon::parse("{$scheduleDate} 07:{$minute}:00");
                } elseif ($idx < 23) {
                    $status = 'sakit';
                    $checkIn = null;
                } else {
                    $status = 'alpa';
                    $checkIn = null;
                }

                AttendanceRecord::updateOrCreate(
                    [
                        'participant_id' => $participant->id,
                        'apel_schedule_id' => $schedule->id,
                    ],
                    [
                        'rfid_card_id' => $participant->activeRfidCard?->id,
                        'status' => $status,
                        'check_in_time' => $checkIn,
                        'recorded_by' => $timdisMi->id,
                        'device_identifier' => 'SCANNER_MI_GATE',
                        'notes' => $status === 'sakit' ? 'Surat izin dokter' : null,
                    ]
                );
            }
        }
    }
}
