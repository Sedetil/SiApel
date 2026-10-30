<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApelSchedule;
use App\Models\AttendanceEvent;
use App\Models\AttendanceRecord;
use App\Models\Participant;
use App\Models\RfidCard;
use App\Models\SystemSetting;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncController extends Controller
{
    /**
     * Tambahkan header CORS agar Python Monitor lokal (port 5000) dapat mengakses API Laravel
     */
    protected function withCors(JsonResponse $response): JsonResponse
    {
        return $response
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization, X-Requested-With');
    }

    /**
     * Handle preflight OPTIONS request
     */
    public function options(): JsonResponse
    {
        return $this->withCors(response()->json([], 200));
    }

    /**
     * Cek status kesiapan server Laravel untuk sinkronisasi dengan Python lokal
     */
    public function status(): JsonResponse
    {
        try {
            $stats = [
                'mahasiswa_count' => Participant::count(),
                'mahasiswa_active_count' => Participant::where('is_active', true)->count(),
                'schedules_count' => ApelSchedule::count(),
                'schedules_active_count' => ApelSchedule::whereIn('status', ['active', 'scheduled'])->count(),
                'kegiatan_count' => ApelSchedule::count(),
                'kegiatan_active_count' => ApelSchedule::where('status', 'active')->count(),
            ];

            return $this->withCors(response()->json([
                'success' => true,
                'message' => 'Server SiApel siap untuk sinkronisasi',
                'stats' => $stats,
                'timestamp' => now()->toIso8601String(),
            ]));
        } catch (\Throwable $e) {
            return $this->withCors(response()->json([
                'success' => false,
                'message' => 'Gagal memeriksa status: '.$e->getMessage(),
            ], 500));
        }
    }

    /**
     * Tarik seluruh data peserta (mahasiswa) untuk disimpan ke database MySQL lokal Python
     */
    public function mahasiswa(): JsonResponse
    {
        try {
            $participants = Participant::with('activeRfidCard')
                ->orderBy('name', 'asc')
                ->get()
                ->map(function (Participant $participant) {
                    $qrCodeId = $participant->activeRfidCard?->card_uid ?: $participant->identifier;

                    return [
                        'id' => $participant->identifier,
                        'participant_id' => $participant->id,
                        'identifier' => $participant->identifier,
                        'name' => $participant->name,
                        'kompi' => $participant->class_group ?: '-',
                        'jurusan' => $participant->study_program ?: '-',
                        'prodi' => $participant->study_program ?: '-',
                        'email' => null,
                        'no_telp_mahasiswa' => $participant->phone ?: '',
                        'no_telp_ortu' => '',
                        'qr_code_id' => $qrCodeId,
                        'is_active' => $participant->is_active ? 1 : 0,
                    ];
                });

            return $this->withCors(response()->json([
                'success' => true,
                'data' => $participants,
                'count' => $participants->count(),
            ]));
        } catch (\Throwable $e) {
            return $this->withCors(response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data mahasiswa: '.$e->getMessage(),
            ], 500));
        }
    }

    /**
     * Tarik seluruh jadwal apel untuk divalidasi oleh Python di lokal
     */
    public function schedules(): JsonResponse
    {
        try {
            $schedules = ApelSchedule::orderBy('schedule_date', 'asc')
                ->orderBy('start_time', 'asc')
                ->get()
                ->values()
                ->map(function (ApelSchedule $schedule, int $index) {
                    // Hitung selisih menit antara batas tepat waktu dan batas tutup absensi
                    $lateThreshold = Carbon::parse($schedule->late_threshold_time);
                    $attendanceEnd = Carbon::parse($schedule->attendance_end);
                    $graceMinutes = max(0, (int) $lateThreshold->diffInMinutes($attendanceEnd, false));

                    return [
                        'id' => $schedule->id,
                        'hari_ke' => $index + 1,
                        'title' => $schedule->title,
                        'tanggal' => $schedule->schedule_date->format('Y-m-d'),
                        'check_in_start' => $schedule->attendance_start,
                        'check_in_end' => $schedule->late_threshold_time,
                        'attendance_end' => $schedule->attendance_end,
                        'check_out_start' => $schedule->end_time,
                        'check_out_end' => '23:59:59',
                        'grace_period_minutes' => $graceMinutes,
                        'location' => $schedule->location,
                        'status' => $schedule->status,
                        'is_active' => in_array($schedule->status, ['active', 'scheduled'], true) ? 1 : 0,
                    ];
                });

            return $this->withCors(response()->json([
                'success' => true,
                'data' => $schedules,
                'count' => $schedules->count(),
            ]));
        } catch (\Throwable $e) {
            return $this->withCors(response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data jadwal apel: '.$e->getMessage(),
            ], 500));
        }
    }

    /**
     * Tarik daftar sesi/kegiatan apel untuk dropdown pemilihan sesi di Python lokal
     */
    public function kegiatan(): JsonResponse
    {
        try {
            $kegiatanList = ApelSchedule::orderBy('schedule_date', 'desc')
                ->get()
                ->map(function (ApelSchedule $schedule) {
                    return [
                        'id' => $schedule->id,
                        'nama' => $schedule->title,
                        'tanggal_pelaksanaan' => $schedule->schedule_date->format('Y-m-d'),
                        'attendance_start' => $schedule->attendance_start,
                        'late_threshold_time' => $schedule->late_threshold_time,
                        'attendance_end' => $schedule->attendance_end,
                        'is_active' => in_array($schedule->status, ['active', 'scheduled'], true) ? 1 : 0,
                    ];
                });

            return $this->withCors(response()->json([
                'success' => true,
                'data' => $kegiatanList,
                'count' => $kegiatanList->count(),
            ]));
        } catch (\Throwable $e) {
            return $this->withCors(response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data kegiatan apel: '.$e->getMessage(),
            ], 500));
        }
    }

    /**
     * Tarik konfigurasi sistem untuk Python lokal
     */
    public function systemConfig(): JsonResponse
    {
        try {
            $configs = SystemSetting::all()->map(function (SystemSetting $setting) {
                return [
                    'config_key' => $setting->key,
                    'config_value' => (string) $setting->value,
                    'description' => $setting->group,
                ];
            });

            // Pastikan selalu ada nilai default grace period jika belum disetel
            if (! $configs->contains('config_key', 'attendance_grace_period_minutes')) {
                $configs->push([
                    'config_key' => 'attendance_grace_period_minutes',
                    'config_value' => '45',
                    'description' => 'Grace period keterlambatan absensi apel dalam menit',
                ]);
            }

            return $this->withCors(response()->json([
                'success' => true,
                'data' => $configs->values(),
                'count' => $configs->count(),
            ]));
        } catch (\Throwable $e) {
            return $this->withCors(response()->json([
                'success' => false,
                'message' => 'Gagal mengambil konfigurasi sistem: '.$e->getMessage(),
            ], 500));
        }
    }

    /**
     * Terima push data presensi dari sistem Python lokal ke database utama (online)
     */
    public function pushAttendance(Request $request): JsonResponse
    {
        try {
            $data = $request->input('data', []);
            if (! is_array($data)) {
                return $this->withCors(response()->json([
                    'success' => false,
                    'message' => 'Format data sinkronisasi tidak valid.',
                ], 422));
            }

            $syncedCount = 0;
            $rejectedCount = 0;
            $rejectionReasons = [];
            $syncedLocalIds = [];

            foreach ($data as $record) {
                if (! isset($record['mahasiswa_id'])) {
                    continue;
                }

                $localId = $record['id'] ?? null;
                $rawIdentifier = trim((string) $record['mahasiswa_id']);
                $cleanIdentifier = preg_replace('/[^a-zA-Z0-9\-_]/', '', $rawIdentifier);
                $withoutQrPrefix = preg_replace('/^(QR-|-)/i', '', $cleanIdentifier);

                // Cari peserta berdasarkan identifier (NPM), ID, atau UID kartu/QR
                $participant = Participant::with('activeRfidCard')
                    ->where('identifier', $cleanIdentifier)
                    ->orWhere('identifier', $withoutQrPrefix)
                    ->orWhereHas('rfidCards', function ($q) use ($cleanIdentifier, $withoutQrPrefix) {
                        $q->whereIn('card_uid', [$cleanIdentifier, $withoutQrPrefix])
                            ->where('is_active', true);
                    })
                    ->first();

                if (! $participant && is_numeric($withoutQrPrefix)) {
                    $participant = Participant::with('activeRfidCard')->find((int) $withoutQrPrefix);
                }

                if (! $participant) {
                    $rejectedCount++;
                    $rejectionReasons[] = "Peserta tidak ditemukan untuk kode QR: {$rawIdentifier}";
                    continue;
                }

                if (! $participant->isActive()) {
                    $rejectedCount++;
                    $rejectionReasons[] = "Peserta {$participant->name} ({$participant->identifier}) berstatus nonaktif";
                    continue;
                }

                // Tentukan jadwal apel terkait
                $scheduleId = ! empty($record['kegiatan_id']) ? (int) $record['kegiatan_id'] : null;
                $schedule = null;

                if ($scheduleId) {
                    $schedule = ApelSchedule::find($scheduleId);
                    if (! $schedule) {
                        $rejectedCount++;
                        $rejectionReasons[] = "Jadwal Apel ID #{$scheduleId} tidak ditemukan (Peserta: {$participant->name})";
                        continue;
                    }
                } else {
                    // Cari jadwal apel berdasarkan tanggal check_in atau hari ini
                    $targetDate = Carbon::today()->format('Y-m-d');
                    if (! empty($record['check_in']) && strlen((string) $record['check_in']) > 8) {
                        try {
                            $targetDate = Carbon::parse($record['check_in'])->format('Y-m-d');
                        } catch (\Throwable) {
                            $targetDate = Carbon::today()->format('Y-m-d');
                        }
                    }

                    $schedule = ApelSchedule::whereDate('schedule_date', $targetDate)
                        ->whereIn('status', ['active', 'scheduled', 'completed'])
                        ->orderByRaw("CASE WHEN status = 'active' THEN 0 ELSE 1 END")
                        ->first();

                    if (! $schedule) {
                        $schedule = ApelSchedule::where('status', 'active')->first();
                    }

                    if (! $schedule) {
                        $rejectedCount++;
                        $rejectionReasons[] = "Tidak ada jadwal apel aktif untuk tanggal {$targetDate} (Peserta: {$participant->name})";
                        continue;
                    }
                }

                $scheduleDateStr = $schedule->schedule_date->format('Y-m-d');

                // Parse waktu check_in dan check_out
                $checkInCarbon = $this->parseSyncTimestamp($record['check_in'] ?? null, $scheduleDateStr) ?? Carbon::now();
                $checkOutCarbon = $this->parseSyncTimestamp($record['check_out'] ?? null, $scheduleDateStr);

                // Validasi waktu check_in hanya bila bukan pemilihan kegiatan eksplisit yang bypass
                if (! $scheduleId) {
                    $checkInTimeOnly = $checkInCarbon->format('H:i:s');
                    $windowStart = Carbon::parse($schedule->attendance_start)->format('H:i:s');
                    $windowEnd = Carbon::parse($schedule->attendance_end)->format('H:i:s');

                    if ($checkInTimeOnly < $windowStart) {
                        $rejectedCount++;
                        $rejectionReasons[] = "Presensi terlalu awal ({$participant->name}: {$checkInTimeOnly}, dibuka: {$windowStart})";
                        continue;
                    }

                    if ($checkInTimeOnly > $windowEnd) {
                        $rejectedCount++;
                        $rejectionReasons[] = "Presensi melewati batas akhir ({$participant->name}: {$checkInTimeOnly}, tutup: {$windowEnd})";
                        continue;
                    }
                }

                // Tentukan status kehadiran: prioritaskan flag is_late dari Python lokal, atau hitung dari jadwal
                if (array_key_exists('is_late', $record)) {
                    $attendanceStatus = ((bool) $record['is_late']) ? 'terlambat' : 'hadir';
                } else {
                    $attendanceStatus = $schedule->determineStatus($checkInCarbon);
                }

                $rfidCardId = $participant->activeRfidCard?->id;
                $cardUidForEvent = $participant->activeRfidCard?->card_uid ?? $participant->identifier;

                DB::transaction(function () use (
                    $schedule,
                    $participant,
                    $rfidCardId,
                    $cardUidForEvent,
                    $checkInCarbon,
                    $checkOutCarbon,
                    $attendanceStatus,
                    $record
                ) {
                    $existingRecord = AttendanceRecord::where('apel_schedule_id', $schedule->id)
                        ->where('participant_id', $participant->id)
                        ->lockForUpdate()
                        ->first();

                    $lateNotes = ! empty($record['late_duration'])
                        ? "Sinkronisasi QR Lokal (Terlambat {$record['late_duration']} menit)"
                        : 'Sinkronisasi QR Lokal';

                    if ($existingRecord) {
                        $existingRecord->update([
                            'check_in_time' => $existingRecord->check_in_time ?? $checkInCarbon,
                            'check_out_time' => $checkOutCarbon ?? $existingRecord->check_out_time,
                            'status' => $existingRecord->status ?: $attendanceStatus,
                            'device_identifier' => $existingRecord->device_identifier ?: 'python-qr-local',
                            'notes' => $existingRecord->notes ?: $lateNotes,
                        ]);
                    } else {
                        AttendanceRecord::create([
                            'apel_schedule_id' => $schedule->id,
                            'participant_id' => $participant->id,
                            'rfid_card_id' => $rfidCardId,
                            'check_in_time' => $checkInCarbon,
                            'check_out_time' => $checkOutCarbon,
                            'status' => $attendanceStatus,
                            'device_identifier' => 'python-qr-local',
                            'notes' => $lateNotes,
                        ]);
                    }

                    AttendanceEvent::create([
                        'apel_schedule_id' => $schedule->id,
                        'card_uid' => substr((string) $cardUidForEvent, 0, 64),
                        'participant_id' => $participant->id,
                        'scan_type' => $checkOutCarbon ? 'check_out' : 'check_in',
                        'scanned_at' => $checkOutCarbon ?? $checkInCarbon,
                        'result' => 'success',
                        'status_determined' => $attendanceStatus,
                        'rejection_reason' => null,
                        'device_identifier' => 'python-qr-local',
                    ]);
                });

                $syncedCount++;
                if ($localId) {
                    $syncedLocalIds[] = $localId;
                }
            }

            $message = "Berhasil menyinkronkan {$syncedCount} data presensi";
            if ($rejectedCount > 0) {
                $message .= ", {$rejectedCount} data ditolak";
            }

            return $this->withCors(response()->json([
                'success' => true,
                'message' => $message,
                'synced_count' => $syncedCount,
                'rejected_count' => $rejectedCount,
                'rejection_reasons' => $rejectionReasons,
                'synced_ids' => $syncedLocalIds,
            ]));
        } catch (\Throwable $e) {
            Log::error('Gagal sinkronisasi presensi dari Python lokal: '.$e->getMessage());

            return $this->withCors(response()->json([
                'success' => false,
                'message' => 'Gagal memproses sinkronisasi: '.$e->getMessage(),
            ], 500));
        }
    }

    /**
     * Helper untuk mengonversi string jam ('07:15:00') atau datetime penuh menjadi objek Carbon
     */
    protected function parseSyncTimestamp(?string $timeValue, string $defaultDate): ?Carbon
    {
        if (empty($timeValue)) {
            return null;
        }

        $trimmed = trim($timeValue);

        // Jika hanya berupa jam HH:MM:SS atau HH:MM
        if (preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $trimmed)) {
            return Carbon::parse("{$defaultDate} {$trimmed}");
        }

        return Carbon::parse($trimmed);
    }
}

