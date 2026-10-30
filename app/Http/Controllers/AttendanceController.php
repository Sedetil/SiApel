<?php

namespace App\Http\Controllers;

use App\Models\ApelSchedule;
use App\Models\AttendanceRecord;
use App\Models\Participant;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function __construct(
        protected AttendanceService $attendanceService
    ) {}

    /**
     * Halaman Live Scanner & Monitor Sesi Apel
     */
    public function monitor(Request $request): View
    {
        $today = Carbon::today()->format('Y-m-d');

        // Pilih jadwal aktif atau berdasarkan pilihan dropdown
        $targetScheduleId = $request->input('schedule_id');

        if ($targetScheduleId) {
            $schedule = ApelSchedule::find($targetScheduleId);
        } else {
            $schedule = ApelSchedule::where('status', 'active')->first();
            if (! $schedule) {
                $schedule = ApelSchedule::whereDate('schedule_date', $today)->first();
            }
        }

        $allSchedules = ApelSchedule::whereIn('status', ['active', 'scheduled'])
            ->orWhereDate('schedule_date', $today)
            ->orderBy('schedule_date', 'desc')
            ->get();

        $stats = [
            'total_participants' => Participant::active()->count(),
            'total_hadir' => 0,
            'total_terlambat' => 0,
            'total_recorded' => 0,
        ];

        $recentRecords = collect();

        if ($schedule) {
            $records = AttendanceRecord::with('participant')
                ->where('apel_schedule_id', $schedule->id)
                ->get();

            $stats['total_hadir'] = $records->where('status', 'hadir')->count();
            $stats['total_terlambat'] = $records->where('status', 'terlambat')->count();
            $stats['total_recorded'] = $records->count();

            $recentRecords = AttendanceRecord::with('participant')
                ->where('apel_schedule_id', $schedule->id)
                ->orderBy('check_in_time', 'desc')
                ->limit(10)
                ->get();
        }

        return view('pages.attendance.monitor', compact(
            'schedule',
            'allSchedules',
            'stats',
            'recentRecords'
        ));
    }

    /**
     * Endpoint Read-Only: Polling Data Live Feed Monitoring Sesi Apel
     */
    public function liveFeed(Request $request): JsonResponse
    {
        $scheduleId = (int) $request->input('schedule_id');
        $schedule = ApelSchedule::find($scheduleId);

        if (! $schedule) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi apel tidak ditemukan.',
            ], 404);
        }

        $records = AttendanceRecord::with('participant')
            ->where('apel_schedule_id', $schedule->id)
            ->get();

        $stats = [
            'total_participants' => Participant::active()->count(),
            'total_hadir' => $records->where('status', 'hadir')->count(),
            'total_terlambat' => $records->where('status', 'terlambat')->count(),
            'total_recorded' => $records->count(),
        ];

        $recentRecords = AttendanceRecord::with('participant')
            ->where('apel_schedule_id', $schedule->id)
            ->orderBy('check_in_time', 'desc')
            ->limit(15)
            ->get()
            ->map(function ($rec) {
                return [
                    'id' => $rec->id,
                    'participant_id' => $rec->participant_id,
                    'identifier' => $rec->participant?->identifier ?? '-',
                    'name' => $rec->participant?->name ?? '-',
                    'study_program' => $rec->participant?->study_program ?? '-',
                    'class_group' => $rec->participant?->class_group ?? '-',
                    'status' => $rec->status,
                    'check_in_time' => $rec->check_in_time ? $rec->check_in_time->format('H:i:s') : '-',
                    'check_out_time' => $rec->check_out_time ? $rec->check_out_time->format('H:i:s') : null,
                ];
            });

        return response()->json([
            'success' => true,
            'stats' => $stats,
            'recent_records' => $recentRecords,
            'timestamp' => now()->format('H:i:s'),
        ]);
    }

    /**
     * FR-07: Riwayat Absensi
     */
    public function history(Request $request): View
    {
        $query = AttendanceRecord::with(['participant', 'schedule', 'recorder']);

        // Filter jadwal apel
        if ($request->filled('schedule_id')) {
            $query->where('apel_schedule_id', $request->input('schedule_id'));
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filter tanggal
        if ($request->filled('date')) {
            $date = $request->input('date');
            $query->whereHas('schedule', function ($q) use ($date) {
                $q->whereDate('schedule_date', $date);
            });
        }

        // Filter pencarian peserta (NPM atau Nama)
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->whereHas('participant', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('identifier', 'like', "%{$search}%");
            });
        }

        // Filter program studi
        if ($request->filled('study_program')) {
            $prodi = $request->input('study_program');
            $query->whereHas('participant', function ($q) use ($prodi) {
                $q->where('study_program', $prodi);
            });
        }

        // Filter kelas
        if ($request->filled('class_group')) {
            $classGroup = $request->input('class_group');
            $query->whereHas('participant', function ($q) use ($classGroup) {
                $q->where('class_group', $classGroup);
            });
        }

        $records = $query->orderBy('check_in_time', 'desc')
            ->paginate(15)
            ->withQueryString();

        $schedules = ApelSchedule::orderBy('schedule_date', 'desc')->get();
        $studyPrograms = Participant::select('study_program')
            ->whereNotNull('study_program')
            ->distinct()
            ->pluck('study_program');

        $classes = Participant::select('class_group')
            ->whereNotNull('class_group')
            ->distinct()
            ->pluck('class_group');

        return view('pages.attendance.history', compact('records', 'schedules', 'studyPrograms', 'classes'));
    }
}
