<?php

namespace App\Services;

use App\Models\ApelSchedule;
use App\Models\AttendanceEvent;
use App\Models\AttendanceRecord;
use App\Models\RfidCard;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AttendanceService
{
    /**
     * Memproses pemindaian kartu RFID untuk absensi (Check-in atau Check-out)
     *
     * @param  string  $cardUid  UID kartu RFID (hex / desimal)
     * @param  int  $scheduleId  ID jadwal apel
     * @param  string  $scanType  'check_in' atau 'check_out'
     * @param  string|null  $deviceIdentifier  ID perangkat pemindai (Android / Reader USB)
     * @param  int|null  $recordedBy  ID user petugas/admin yang menjalankan sesi
     * @param  Carbon|null  $customServerTime  Waktu tepercaya server (opsional untuk testing)
     * @return array Hasil pemrosesan absensi
     */
    public function processScan(
        string $cardUid,
        int $scheduleId,
        string $scanType = 'check_in',
        ?string $deviceIdentifier = null,
        ?int $recordedBy = null,
        ?Carbon $customServerTime = null
    ): array {
        $now = $customServerTime ?? Carbon::now();
        $cleanUid = trim(strtoupper($cardUid));

        // 1. Validasi Jadwal Apel
        $schedule = ApelSchedule::find($scheduleId);
        if (! $schedule) {
            $this->logEvent(
                scheduleId: null,
                cardUid: $cleanUid,
                participantId: null,
                scanType: $scanType,
                scannedAt: $now,
                result: 'rejected',
                statusDetermined: null,
                reason: 'schedule_not_found',
                device: $deviceIdentifier
            );

            return [
                'success' => false,
                'code' => 'SCHEDULE_NOT_FOUND',
                'message' => 'Jadwal apel tidak ditemukan dalam sistem.',
            ];
        }

        // 2. Validasi Status Sesi Apel (harus aktif)
        if ($schedule->status !== 'active') {
            $this->logEvent(
                scheduleId: $schedule->id,
                cardUid: $cleanUid,
                participantId: null,
                scanType: $scanType,
                scannedAt: $now,
                result: 'rejected',
                statusDetermined: null,
                reason: 'schedule_inactive',
                device: $deviceIdentifier
            );

            return [
                'success' => false,
                'code' => 'SCHEDULE_INACTIVE',
                'message' => 'Sesi apel ini sedang tidak aktif.',
                'schedule_title' => $schedule->title,
                'schedule_status' => $schedule->status,
            ];
        }

        // 3. Validasi Periode Waktu Absensi (attendance_start s.d. attendance_end)
        if (! $schedule->isWithinAttendanceWindow($now)) {
            $this->logEvent(
                scheduleId: $schedule->id,
                cardUid: $cleanUid,
                participantId: null,
                scanType: $scanType,
                scannedAt: $now,
                result: 'rejected',
                statusDetermined: null,
                reason: 'out_of_period',
                device: $deviceIdentifier
            );

            return [
                'success' => false,
                'code' => 'OUT_OF_ATTENDANCE_PERIOD',
                'message' => "Pemindaian di luar periode absensi ({$schedule->attendance_start} - {$schedule->attendance_end}).",
                'attendance_window' => [
                    'start' => $schedule->attendance_start,
                    'end' => $schedule->attendance_end,
                    'server_time' => $now->format('H:i:s'),
                ],
            ];
        }

        // 4. Cari Kartu RFID Aktif
        $card = RfidCard::with('participant')
            ->where('card_uid', $cleanUid)
            ->where('is_active', true)
            ->first();

        if (! $card || ! $card->participant) {
            $this->logEvent(
                scheduleId: $schedule->id,
                cardUid: $cleanUid,
                participantId: null,
                scanType: $scanType,
                scannedAt: $now,
                result: 'rejected',
                statusDetermined: null,
                reason: 'card_not_registered',
                device: $deviceIdentifier
            );

            return [
                'success' => false,
                'code' => 'CARD_NOT_REGISTERED',
                'message' => 'Kartu RFID belum terdaftar atau telah dinonaktifkan.',
                'card_uid' => $cleanUid,
            ];
        }

        $participant = $card->participant;

        // 5. Validasi Status Peserta
        if (! $participant->isActive()) {
            $this->logEvent(
                scheduleId: $schedule->id,
                cardUid: $cleanUid,
                participantId: $participant->id,
                scanType: $scanType,
                scannedAt: $now,
                result: 'rejected',
                statusDetermined: null,
                reason: 'participant_inactive',
                device: $deviceIdentifier
            );

            return [
                'success' => false,
                'code' => 'PARTICIPANT_INACTIVE',
                'message' => "Peserta {$participant->name} dalam status nonaktif.",
                'participant' => [
                    'identifier' => $participant->identifier,
                    'name' => $participant->name,
                ],
            ];
        }

        // 6. Tangani Transaksi Database Berdasarkan Scan Type
        return DB::transaction(function () use ($schedule, $participant, $card, $cleanUid, $scanType, $now, $deviceIdentifier, $recordedBy) {
            // Absensi Keluar (Check-out)
            if ($scanType === 'check_out') {
                $record = AttendanceRecord::where('apel_schedule_id', $schedule->id)
                    ->where('participant_id', $participant->id)
                    ->lockForUpdate()
                    ->first();

                if (! $record) {
                    $this->logEvent(
                        scheduleId: $schedule->id,
                        cardUid: $cleanUid,
                        participantId: $participant->id,
                        scanType: 'check_out',
                        scannedAt: $now,
                        result: 'rejected',
                        statusDetermined: null,
                        reason: 'no_check_in_record',
                        device: $deviceIdentifier
                    );

                    return [
                        'success' => false,
                        'code' => 'NO_CHECK_IN_RECORD',
                        'message' => 'Peserta belum melakukan absensi masuk pada sesi ini.',
                        'participant' => [
                            'identifier' => $participant->identifier,
                            'name' => $participant->name,
                        ],
                    ];
                }

                if ($record->check_out_time !== null) {
                    $this->logEvent(
                        scheduleId: $schedule->id,
                        cardUid: $cleanUid,
                        participantId: $participant->id,
                        scanType: 'check_out',
                        scannedAt: $now,
                        result: 'rejected',
                        statusDetermined: $record->status,
                        reason: 'already_checked_out',
                        device: $deviceIdentifier
                    );

                    return [
                        'success' => false,
                        'code' => 'ALREADY_CHECKED_OUT',
                        'message' => 'Peserta sudah mencatatkan waktu keluar sebelumnya.',
                        'check_out_time' => $record->check_out_time->format('H:i:s'),
                        'participant' => [
                            'identifier' => $participant->identifier,
                            'name' => $participant->name,
                        ],
                    ];
                }

                $record->update([
                    'check_out_time' => $now,
                ]);

                $this->logEvent(
                    scheduleId: $schedule->id,
                    cardUid: $cleanUid,
                    participantId: $participant->id,
                    scanType: 'check_out',
                    scannedAt: $now,
                    result: 'success',
                    statusDetermined: $record->status,
                    reason: null,
                    device: $deviceIdentifier
                );

                return [
                    'success' => true,
                    'code' => 'CHECK_OUT_SUCCESS',
                    'message' => "Absensi keluar berhasil dicatat untuk {$participant->name}.",
                    'participant' => [
                        'identifier' => $participant->identifier,
                        'name' => $participant->name,
                        'study_program' => $participant->study_program,
                        'class_group' => $participant->class_group,
                    ],
                    'attendance' => [
                        'status' => $record->status,
                        'check_in_time' => $record->check_in_time?->format('H:i:s'),
                        'check_out_time' => $now->format('H:i:s'),
                    ],
                ];
            }

            // Absensi Masuk (Check-in)
            // Cek duplikasi absensi masuk menggunakan lockForUpdate
            $existing = AttendanceRecord::where('apel_schedule_id', $schedule->id)
                ->where('participant_id', $participant->id)
                ->lockForUpdate()
                ->first();

            if ($existing) {
                $this->logEvent(
                    scheduleId: $schedule->id,
                    cardUid: $cleanUid,
                    participantId: $participant->id,
                    scanType: 'check_in',
                    scannedAt: $now,
                    result: 'rejected',
                    statusDetermined: $existing->status,
                    reason: 'duplicate_scan',
                    device: $deviceIdentifier
                );

                return [
                    'success' => false,
                    'code' => 'DUPLICATE_SCAN',
                    'message' => "Peserta {$participant->name} sudah tercatat ({$existing->status}) pada {$existing->check_in_time->format('H:i:s')}.",
                    'already_recorded' => true,
                    'participant' => [
                        'identifier' => $participant->identifier,
                        'name' => $participant->name,
                        'study_program' => $participant->study_program,
                        'class_group' => $participant->class_group,
                    ],
                    'attendance' => [
                        'status' => $existing->status,
                        'check_in_time' => $existing->check_in_time->format('H:i:s'),
                    ],
                ];
            }

            // Tentukan status hadir atau terlambat berbasis waktu backend
            $attendanceStatus = $schedule->determineStatus($now);

            $record = AttendanceRecord::create([
                'apel_schedule_id' => $schedule->id,
                'participant_id' => $participant->id,
                'rfid_card_id' => $card->id,
                'check_in_time' => $now,
                'status' => $attendanceStatus,
                'recorded_by' => $recordedBy,
                'device_identifier' => $deviceIdentifier,
            ]);

            $this->logEvent(
                scheduleId: $schedule->id,
                cardUid: $cleanUid,
                participantId: $participant->id,
                scanType: 'check_in',
                scannedAt: $now,
                result: 'success',
                statusDetermined: $attendanceStatus,
                reason: null,
                device: $deviceIdentifier
            );

            return [
                'success' => true,
                'code' => 'CHECK_IN_SUCCESS',
                'message' => $attendanceStatus === 'hadir'
                    ? "Absensi berhasil: Hadir Tepat Waktu ({$participant->name})"
                    : "Absensi tercatat: Terlambat Apel ({$participant->name})",
                'participant' => [
                    'identifier' => $participant->identifier,
                    'name' => $participant->name,
                    'study_program' => $participant->study_program,
                    'class_group' => $participant->class_group,
                ],
                'attendance' => [
                    'status' => $attendanceStatus,
                    'check_in_time' => $now->format('H:i:s'),
                    'late_threshold' => $schedule->late_threshold_time,
                ],
            ];
        });
    }

    /**
     * Catat log event scan ke tabel attendance_events
     */
    protected function logEvent(
        ?int $scheduleId,
        string $cardUid,
        ?int $participantId,
        string $scanType,
        Carbon $scannedAt,
        string $result,
        ?string $statusDetermined,
        ?string $reason,
        ?string $device
    ): void {
        try {
            AttendanceEvent::create([
                'apel_schedule_id' => $scheduleId,
                'card_uid' => $cardUid,
                'participant_id' => $participantId,
                'scan_type' => $scanType,
                'scanned_at' => $scannedAt,
                'result' => $result,
                'status_determined' => $statusDetermined,
                'rejection_reason' => $reason,
                'device_identifier' => $device,
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to write AttendanceEvent log: '.$e->getMessage());
        }
    }
}
