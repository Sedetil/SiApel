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
        $user = auth()->user();
        $query = AttendanceRecord::with(['participant', 'schedule', 'recorder']);

        // Scope Timdis/Officer binaan jika bukan admin
        if ($user && ! $user->isAdmin()) {
            $query->forUser($user);
        }

        // Tanggal Apel yang sedang dipilih
        // Jika ada input 'date', gunakan input tersebut
        // Jika tidak ada input 'date', ambil tanggal jadwal apel aktif / jadwal apel terbaru yang ada di database
        $availableDates = ApelSchedule::orderBy('schedule_date', 'desc')
            ->get()
            ->pluck('schedule_date')
            ->map(function ($d) {
                return Carbon::parse($d)->format('Y-m-d');
            })
            ->unique()
            ->values();

        $selectedDate = null;
        if ($request->filled('date')) {
            $dateInput = trim($request->input('date'));
            if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $dateInput)) {
                try {
                    $selectedDate = Carbon::createFromFormat('d/m/Y', $dateInput)->format('Y-m-d');
                } catch (\Exception $e) {
                    $selectedDate = $dateInput;
                }
            } else {
                $selectedDate = $dateInput;
            }
        } elseif ($request->filled('schedule_id')) {
            $sched = ApelSchedule::find($request->input('schedule_id'));
            if ($sched) {
                $selectedDate = Carbon::parse($sched->schedule_date)->format('Y-m-d');
            }
        }

        // Jika tidak ada filter tanggal spesifik dari request, jangan batasi atau default ke jadwal terbaru jika diinginkan
        if ($selectedDate) {
            $query->where(function ($q) use ($selectedDate) {
                $q->whereHas('schedule', function ($sq) use ($selectedDate) {
                    $sq->whereDate('schedule_date', $selectedDate);
                })->orWhereDate('check_in_time', $selectedDate);
            });
        }

        // Filter jadwal apel langsung
        if ($request->filled('schedule_id')) {
            $query->where('apel_schedule_id', $request->input('schedule_id'));
        }

        // Filter pencarian peserta (NPM atau Nama)
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->whereHas('participant', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('identifier', 'like', "%{$search}%");
            });
        }

        // Filter jurusan
        if ($request->filled('department')) {
            $dept = $request->input('department');
            $query->whereHas('participant', function ($q) use ($dept) {
                $q->where('department', $dept);
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

        // Filter status kehadiran
        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'tidak_hadir') {
                $query->whereIn('status', ['alpa', 'tidak_hadir']);
            } elseif ($status === 'sakit_izin') {
                $query->whereIn('status', ['sakit', 'izin']);
            } else {
                $query->where('status', $status);
            }
        }

        // Summary metrics & Status counts query (berdasarkan filter tanggal, jurusan, prodi, dan search yang sedang aktif, tanpa filter status)
        $baseMetricsQuery = AttendanceRecord::query();
        if ($user && ! $user->isAdmin()) {
            $baseMetricsQuery->forUser($user);
        }
        if ($request->filled('schedule_id')) {
            $baseMetricsQuery->where('apel_schedule_id', $request->input('schedule_id'));
        }
        if ($selectedDate) {
            $baseMetricsQuery->where(function ($q) use ($selectedDate) {
                $q->whereHas('schedule', function ($sq) use ($selectedDate) {
                    $sq->whereDate('schedule_date', $selectedDate);
                })->orWhereDate('check_in_time', $selectedDate);
            });
        }
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $baseMetricsQuery->whereHas('participant', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('identifier', 'like', "%{$search}%");
            });
        }
        if ($request->filled('department')) {
            $dept = $request->input('department');
            $baseMetricsQuery->whereHas('participant', function ($q) use ($dept) {
                $q->where('department', $dept);
            });
        }
        if ($request->filled('study_program')) {
            $prodi = $request->input('study_program');
            $baseMetricsQuery->whereHas('participant', function ($q) use ($prodi) {
                $q->where('study_program', $prodi);
            });
        }

        // Total target mahasiswa binaan untuk konteks ini
        $totalParticipantsTarget = Participant::forUser($user)->active()->count();
        if ($totalParticipantsTarget === 0) {
            $totalParticipantsTarget = Participant::forUser($user)->count();
        }

        // Menghitung angka status counts untuk bar status pills
        $countSemua = (clone $baseMetricsQuery)->count();
        $countHadir = (clone $baseMetricsQuery)->where('status', 'hadir')->count();
        $countTerlambat = (clone $baseMetricsQuery)->where('status', 'terlambat')->count();
        $countSakit = (clone $baseMetricsQuery)->where('status', 'sakit')->count();
        $countIzin = (clone $baseMetricsQuery)->where('status', 'izin')->count();
        $countTidakHadir = (clone $baseMetricsQuery)->whereIn('status', ['alpa', 'tidak_hadir'])->count();

        // 4 Summary cards
        $cardHadirCount = $countHadir;
        $cardHadirPct = $totalParticipantsTarget > 0 ? round(($cardHadirCount / $totalParticipantsTarget) * 100, 1) : 0;

        $cardTerlambatCount = $countTerlambat;
        $cardTerlambatPct = $totalParticipantsTarget > 0 ? round(($cardTerlambatCount / $totalParticipantsTarget) * 100, 1) : 0;

        $cardSakitIzinCount = $countSakit + $countIzin;
        $cardSakitIzinPct = $totalParticipantsTarget > 0 ? round(($cardSakitIzinCount / $totalParticipantsTarget) * 100, 1) : 0;

        // Tanpa Keterangan / Alpha: bisa dari status alpa atau selisih target peserta yang belum absen
        $cardAlphaCount = $countTidakHadir > 0 ? $countTidakHadir : max(0, $totalParticipantsTarget - $countSemua);
        $cardAlphaPct = $totalParticipantsTarget > 0 ? round(($cardAlphaCount / $totalParticipantsTarget) * 100, 1) : 0;

        $summaryStats = [
            'total_target' => $totalParticipantsTarget,
            'hadir' => [
                'count' => $cardHadirCount,
                'pct' => $cardHadirPct,
            ],
            'terlambat' => [
                'count' => $cardTerlambatCount,
                'pct' => $cardTerlambatPct,
            ],
            'sakit_izin' => [
                'count' => $cardSakitIzinCount,
                'pct' => $cardSakitIzinPct,
            ],
            'alpha' => [
                'count' => $cardAlphaCount,
                'pct' => $cardAlphaPct,
            ],
            'counts' => [
                'semua' => $countSemua,
                'hadir' => $countHadir,
                'terlambat' => $countTerlambat,
                'sakit' => $countSakit,
                'tidak_hadir' => $countTidakHadir > 0 ? $countTidakHadir : $cardAlphaCount,
            ],
        ];

        // Pilihan per halaman (default 10)
        $perPage = (int) $request->input('per_page', 10);
        if (! in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 10;
        }

        // Data mahasiswa per halaman
        $records = $query->orderBy('check_in_time', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        $schedules = ApelSchedule::orderBy('schedule_date', 'desc')->get();

        // Data Dropdown Jurusan & Program Studi
        $departmentsQuery = Participant::select('department')->whereNotNull('department');
        if ($user && ! $user->isAdmin() && ! empty($user->department)) {
            $departmentsQuery->where('department', $user->department);
        }
        $departments = $departmentsQuery->distinct()->pluck('department');

        $studyProgramsQuery = Participant::select('study_program')->whereNotNull('study_program');
        if ($user && ! $user->isAdmin() && ! empty($user->department)) {
            $studyProgramsQuery->where('department', $user->department);
        }
        if ($request->filled('department')) {
            $studyProgramsQuery->where('department', $request->input('department'));
        }
        $studyPrograms = $studyProgramsQuery->distinct()->pluck('study_program');

        $classesQuery = Participant::select('class_group')
            ->whereNotNull('class_group')
            ->distinct();

        if ($user && ! $user->isAdmin()) {
            $classes = $user->assigned_classes;
            if (! empty($classes)) {
                $classesQuery->whereIn('class_group', $classes);
            } elseif (! empty($user->class_group)) {
                $classesQuery->where('class_group', $user->class_group);
            } elseif (! empty($user->study_program)) {
                $classesQuery->where('study_program', $user->study_program);
            } elseif (! empty($user->department)) {
                $classesQuery->where('department', $user->department);
            }
        }

        $classes = $classesQuery->pluck('class_group');

        return view('pages.attendance.history', compact(
            'records',
            'schedules',
            'availableDates',
            'selectedDate',
            'departments',
            'studyPrograms',
            'classes',
            'summaryStats',
            'perPage'
        ));
    }
}
