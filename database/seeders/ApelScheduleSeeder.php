<?php

namespace Database\Seeders;

use App\Models\ApelSchedule;
use App\Models\AttendanceEvent;
use App\Models\AttendanceRecord;
use App\Models\Participant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ApelScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $adminId = $admin?->id;

        $today = Carbon::today();
        $todayStr = $today->format('Y-m-d');
        $tomorrowStr = Carbon::tomorrow()->format('Y-m-d');

        // Sesi apel hari ini (Aktif)
        $todaySchedule = ApelSchedule::updateOrCreate(
            ['title' => 'Apel Kedisiplinan Pagi Kampus POLINELA', 'schedule_date' => $todayStr],
            [
                'category' => 'rutin',
                'start_time' => '07:00:00',
                'end_time' => '08:00:00',
                'attendance_start' => '06:30:00',
                'attendance_end' => '08:00:00',
                'late_threshold_time' => '07:15:00', // Toleransi 15 menit
                'location' => 'Lapangan Apel Utama POLINELA',
                'status' => 'active',
                'created_by' => $adminId,
                'description' => 'Apel kedisiplinan rutin mahasiswa dan civitas akademika.',
            ]
        );

        // Sesi apel mendatang
        ApelSchedule::updateOrCreate(
            ['title' => 'Apel Pagi Mingguan Kompi A & B', 'schedule_date' => $tomorrowStr],
            [
                'category' => 'rutin',
                'start_time' => '07:00:00',
                'end_time' => '08:00:00',
                'attendance_start' => '06:30:00',
                'attendance_end' => '08:00:00',
                'late_threshold_time' => '07:10:00',
                'location' => 'Lapangan Apel Gedung Utama',
                'status' => 'scheduled',
                'created_by' => $adminId,
                'description' => 'Apel kedisiplinan persiapan kegiatan lapangan.',
            ]
        );

        $activeParticipants = Participant::active()->with('activeRfidCard')->get();
        if ($activeParticipants->isEmpty()) {
            return;
        }

        // ==========================================
        // 1. DATA ABSENSI HARI INI (REAL DATA)
        // ==========================================
        // Dari peserta aktif yang ada:
        // - 5 Hadir tepat waktu
        // - 1 Terlambat
        // - 1 Izin
        // - 1 Belum/Tidak Hadir
        $todayIndex = 0;
        foreach ($activeParticipants as $participant) {
            $card = $participant->activeRfidCard;
            $cardId = $card?->id;

            if ($todayIndex < 5) {
                // Hadir tepat waktu (antara 06:40 - 07:10)
                $checkIn = Carbon::parse("{$todayStr} 06:45:00")->addMinutes($todayIndex * 5);
                AttendanceRecord::updateOrCreate(
                    ['apel_schedule_id' => $todaySchedule->id, 'participant_id' => $participant->id],
                    [
                        'rfid_card_id' => $cardId,
                        'check_in_time' => $checkIn,
                        'status' => 'hadir',
                        'recorded_by' => $adminId,
                        'device_identifier' => 'READER_GATE_UTAMA',
                        'notes' => 'Presensi RFID tap berhasil',
                    ]
                );

                if ($card) {
                    AttendanceEvent::firstOrCreate(
                        [
                            'apel_schedule_id' => $todaySchedule->id,
                            'participant_id' => $participant->id,
                            'scan_type' => 'check_in',
                        ],
                        [
                            'card_uid' => $card->card_uid,
                            'result' => 'success',
                            'scanned_at' => $checkIn,
                            'status_determined' => 'hadir',
                            'device_identifier' => 'READER_GATE_UTAMA',
                        ]
                    );
                }
            } elseif ($todayIndex === 5) {
                // Terlambat (07:22)
                $checkIn = Carbon::parse("{$todayStr} 07:22:00");
                AttendanceRecord::updateOrCreate(
                    ['apel_schedule_id' => $todaySchedule->id, 'participant_id' => $participant->id],
                    [
                        'rfid_card_id' => $cardId,
                        'check_in_time' => $checkIn,
                        'status' => 'terlambat',
                        'recorded_by' => $adminId,
                        'device_identifier' => 'READER_GATE_UTAMA',
                        'notes' => 'Terlambat 7 menit dari batas toleransi',
                    ]
                );

                if ($card) {
                    AttendanceEvent::firstOrCreate(
                        [
                            'apel_schedule_id' => $todaySchedule->id,
                            'participant_id' => $participant->id,
                            'scan_type' => 'check_in',
                        ],
                        [
                            'card_uid' => $card->card_uid,
                            'result' => 'success',
                            'scanned_at' => $checkIn,
                            'status_determined' => 'terlambat',
                            'device_identifier' => 'READER_GATE_UTAMA',
                        ]
                    );
                }
            } elseif ($todayIndex === 6) {
                // Izin resmi
                AttendanceRecord::updateOrCreate(
                    ['apel_schedule_id' => $todaySchedule->id, 'participant_id' => $participant->id],
                    [
                        'rfid_card_id' => $cardId,
                        'check_in_time' => null,
                        'status' => 'izin',
                        'recorded_by' => $adminId,
                        'device_identifier' => 'MANUAL_DISPEN',
                        'notes' => 'Izin perwakilan lomba kampus',
                    ]
                );
            }
            // Index 7 ke atas dibiarkan tanpa record (Tidak hadir / Alpa)

            $todayIndex++;
        }

        // ==========================================
        // 2. DATA HISTORIS 90 HARI (REAL HISTORICAL DATA)
        // ==========================================
        // Buat jadwal apel dan catatan presensi di hari-hari kerja (Senin s/d Jumat)
        for ($daysAgo = 90; $daysAgo >= 1; $daysAgo--) {
            $pastDate = $today->copy()->subDays($daysAgo);

            // Apel hanya dilaksanakan pada hari kerja (Senin=1 s/d Jumat=5)
            if ($pastDate->isWeekend()) {
                continue;
            }

            $pastDateStr = $pastDate->format('Y-m-d');
            $pastSchedule = ApelSchedule::updateOrCreate(
                ['title' => 'Apel Kedisiplinan Pagi Kampus POLINELA', 'schedule_date' => $pastDateStr],
                [
                    'category' => 'rutin',
                    'start_time' => '07:00:00',
                    'end_time' => '08:00:00',
                    'attendance_start' => '06:30:00',
                    'attendance_end' => '08:00:00',
                    'late_threshold_time' => '07:15:00',
                    'location' => 'Lapangan Apel Utama POLINELA',
                    'status' => 'completed',
                    'created_by' => $adminId,
                    'description' => 'Apel kedisiplinan rutin selesai.',
                ]
            );

            // Pola realistis: sebagian besar hadir, sesekali 1 terlambat atau izin
            $pIndex = 0;
            $modPattern = $daysAgo % 7;

            foreach ($activeParticipants as $participant) {
                $card = $participant->activeRfidCard;
                $cardId = $card?->id;

                // Variasi natural:
                // Hari normal: hampir semua hadir (87.5% - 100%)
                if ($pIndex === 6 && in_array($modPattern, [2, 5])) {
                    // Terlambat
                    AttendanceRecord::updateOrCreate(
                        ['apel_schedule_id' => $pastSchedule->id, 'participant_id' => $participant->id],
                        [
                            'rfid_card_id' => $cardId,
                            'check_in_time' => Carbon::parse("{$pastDateStr} 07:18:00"),
                            'status' => 'terlambat',
                            'recorded_by' => $adminId,
                            'device_identifier' => 'READER_GATE_UTAMA',
                        ]
                    );
                } elseif ($pIndex === 7 && in_array($modPattern, [3])) {
                    // Izin
                    AttendanceRecord::updateOrCreate(
                        ['apel_schedule_id' => $pastSchedule->id, 'participant_id' => $participant->id],
                        [
                            'rfid_card_id' => $cardId,
                            'check_in_time' => null,
                            'status' => 'izin',
                            'recorded_by' => $adminId,
                            'device_identifier' => 'MANUAL_DISPEN',
                            'notes' => 'Izin akademik',
                        ]
                    );
                } elseif ($pIndex === 7 && in_array($modPattern, [6])) {
                    // Tidak ada record (Alpa)
                    continue;
                } else {
                    // Hadir tepat waktu
                    $checkInMin = sprintf('%02d', 35 + (($pIndex * 3) % 20));
                    AttendanceRecord::updateOrCreate(
                        ['apel_schedule_id' => $pastSchedule->id, 'participant_id' => $participant->id],
                        [
                            'rfid_card_id' => $cardId,
                            'check_in_time' => Carbon::parse("{$pastDateStr} 06:{$checkInMin}:00"),
                            'status' => 'hadir',
                            'recorded_by' => $adminId,
                            'device_identifier' => 'READER_GATE_UTAMA',
                        ]
                    );
                }

                $pIndex++;
            }
        }
    }
}
