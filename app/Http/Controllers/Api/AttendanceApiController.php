<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApelSchedule;
use App\Models\AttendanceRecord;
use App\Models\Participant;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AttendanceApiController extends Controller
{
    public function __construct(
        protected AttendanceService $attendanceService
    ) {}

    /**
     * Mendapatkan daftar jadwal apel yang aktif atau dijadwalkan hari ini
     */
    public function activeSchedules(): JsonResponse
    {
        $today = Carbon::today()->format('Y-m-d');

        $schedules = ApelSchedule::where('status', 'active')
            ->orWhere(function ($query) use ($today) {
                $query->where('schedule_date', $today)
                    ->whereIn('status', ['scheduled', 'active']);
            })
            ->orderBy('schedule_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $schedules,
            'server_time' => Carbon::now()->toIso8601String(),
        ]);
    }

    /**
     * Mendapatkan statistik dan riwayat sesi apel untuk petugas Android
     */
    public function scheduleSummary(int $id): JsonResponse
    {
        $schedule = ApelSchedule::find($id);

        if (! $schedule) {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal apel tidak ditemukan.',
            ], 404);
        }

        $totalParticipants = Participant::active()->count();
        $records = AttendanceRecord::with('participant')
            ->where('apel_schedule_id', $schedule->id)
            ->get();

        $totalHadir = $records->where('status', 'hadir')->count();
        $totalTerlambat = $records->where('status', 'terlambat')->count();
        $totalRecorded = $records->count();
        $totalBelumHadir = max(0, $totalParticipants - $totalRecorded);

        $recentRecords = AttendanceRecord::with('participant')
            ->where('apel_schedule_id', $schedule->id)
            ->orderBy('check_in_time', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($rec) {
                return [
                    'id' => $rec->id,
                    'identifier' => $rec->participant?->identifier,
                    'name' => $rec->participant?->name,
                    'study_program' => $rec->participant?->study_program,
                    'status' => $rec->status,
                    'check_in_time' => $rec->check_in_time?->format('H:i:s'),
                    'check_out_time' => $rec->check_out_time?->format('H:i:s'),
                ];
            });

        return response()->json([
            'success' => true,
            'schedule' => [
                'id' => $schedule->id,
                'title' => $schedule->title,
                'schedule_date' => $schedule->schedule_date->format('Y-m-d'),
                'start_time' => $schedule->start_time,
                'end_time' => $schedule->end_time,
                'late_threshold_time' => $schedule->late_threshold_time,
                'status' => $schedule->status,
                'location' => $schedule->location,
            ],
            'statistics' => [
                'total_participants' => $totalParticipants,
                'total_recorded' => $totalRecorded,
                'total_hadir' => $totalHadir,
                'total_terlambat' => $totalTerlambat,
                'total_belum_hadir' => $totalBelumHadir,
                'attendance_rate' => $totalParticipants > 0 ? round(($totalRecorded / $totalParticipants) * 100, 1) : 0,
            ],
            'recent_scans' => $recentRecords,
            'server_time' => Carbon::now()->toIso8601String(),
        ]);
    }

    /**
     * Memproses pemindaian kartu RFID
     */
    public function scan(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'apel_schedule_id' => 'required|integer|exists:apel,id_apel',
            'card_uid' => 'required|string|max:64',
            'scan_type' => 'nullable|string|in:check_in,check_out',
            'device_identifier' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => 'VALIDATION_ERROR',
                'message' => 'Format payload RFID tidak valid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $scheduleId = (int) $request->input('apel_schedule_id');
        $cardUid = $request->input('card_uid');
        $scanType = $request->input('scan_type', 'check_in');
        $deviceId = $request->input('device_identifier', 'api-client');
        $recordedBy = $request->user()?->id;

        $result = $this->attendanceService->processScan(
            cardUid: $cardUid,
            scheduleId: $scheduleId,
            scanType: $scanType,
            deviceIdentifier: $deviceId,
            recordedBy: $recordedBy
        );

        $httpCode = 200;
        if (! $result['success']) {
            $httpCode = match ($result['code']) {
                'SCHEDULE_NOT_FOUND', 'CARD_NOT_REGISTERED' => 404,
                'DUPLICATE_SCAN' => 409,
                default => 422,
            };
        }

        return response()->json($result, $httpCode);
    }

    /**
     * Cek peserta berdasarkan UID kartu atau NPM
     */
    public function lookup(Request $request): JsonResponse
    {
        $query = trim($request->input('q', ''));
        if (empty($query)) {
            return response()->json(['success' => false, 'message' => 'Parameter q wajib diisi.'], 400);
        }

        $participant = Participant::with('activeRfidCard')
            ->where('identifier', $query)
            ->orWhereHas('rfidCards', function ($q) use ($query) {
                $q->where('card_uid', $query)->where('is_active', true);
            })
            ->first();

        if (! $participant) {
            return response()->json([
                'success' => false,
                'message' => 'Peserta tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $participant->id,
                'identifier' => $participant->identifier,
                'name' => $participant->name,
                'study_program' => $participant->study_program,
                'class_group' => $participant->class_group,
                'is_active' => $participant->isActive(),
                'card_uid' => $participant->activeRfidCard?->card_uid,
            ],
        ]);
    }
}
