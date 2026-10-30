<?php

namespace App\Http\Controllers;

use App\Models\ApelSchedule;
use App\Models\AttendanceEvent;
use App\Models\AttendanceRecord;
use App\Models\Participant;
use App\Models\PermissionRequest;
use App\Models\RfidCard;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $today = Carbon::today()->format('Y-m-d');
        $user = auth()->user();
        $isTimdis = $user && ! $user->isAdmin();

        // Jadwal aktif saat ini atau jadwal hari ini
        $activeSchedule = ApelSchedule::where('status', 'active')->first();
        if (! $activeSchedule) {
            $activeSchedule = ApelSchedule::whereDate('schedule_date', $today)->first();
        }
        if (! $activeSchedule) {
            $activeSchedule = ApelSchedule::latest('schedule_date')->first();
        }

        // Metrik global (scoped jika Timdis)
        $totalParticipants = Participant::forUser($user)->count();
        $totalMappedCards = RfidCard::where('is_active', true)
            ->whereHas('participant', function ($q) use ($user) {
                $q->forUser($user);
            })->count();
        $totalSchedules = ApelSchedule::count();

        // Metrik sesi aktif / hari ini
        $targetScheduleId = $request->input('schedule_id', $activeSchedule?->id);
        $selectedSchedule = $targetScheduleId ? ApelSchedule::find($targetScheduleId) : $activeSchedule;

        $sessionHadir = 0;
        $sessionTerlambat = 0;
        $sessionIzin = 0;
        $sessionSakit = 0;
        $sessionTotal = 0;
        $sessionTidakHadir = $totalParticipants;
        $attendanceRate = 0.0;
        $hadirRate = 0.0;
        $terlambatRate = 0.0;
        $izinRate = 0.0;
        $sakitRate = 0.0;
        $tidakHadirRate = $totalParticipants > 0 ? 100.0 : 0.0;
        $recentRecords = collect();

        if ($selectedSchedule) {
            $records = AttendanceRecord::with(['participant', 'rfidCard'])
                ->forUser($user)
                ->where('apel_schedule_id', $selectedSchedule->id)
                ->get();

            $sessionHadir = $records->where('status', 'hadir')->count();
            $sessionTerlambat = $records->where('status', 'terlambat')->count();
            $sessionIzin = $records->where('status', 'izin')->count();
            $sessionSakit = $records->where('status', 'sakit')->count();

            // Total mahasiswa hadir fisik (tepat waktu + terlambat)
            $sessionTotal = $sessionHadir + $sessionTerlambat;

            // Mahasiswa tidak hadir / alpa
            $sessionTidakHadir = max(0, $totalParticipants - ($sessionHadir + $sessionTerlambat + $sessionIzin + $sessionSakit));

            if ($totalParticipants > 0) {
                $attendanceRate = round(($sessionTotal / $totalParticipants) * 100, 1);
                $hadirRate = round(($sessionHadir / $totalParticipants) * 100, 1);
                $terlambatRate = round(($sessionTerlambat / $totalParticipants) * 100, 1);
                $izinRate = round(($sessionIzin / $totalParticipants) * 100, 1);
                $sakitRate = round(($sessionSakit / $totalParticipants) * 100, 1);
                $tidakHadirRate = round(($sessionTidakHadir / $totalParticipants) * 100, 1);
            }

            $recentRecords = AttendanceRecord::with('participant')
                ->forUser($user)
                ->where('apel_schedule_id', $selectedSchedule->id)
                ->orderBy('check_in_time', 'desc')
                ->limit(8)
                ->get();
        }

        $scopeDescription = 'Aktif Terdaftar Dalam Sistem';
        if ($isTimdis) {
            $parts = array_filter([
                $user->department,
                $user->study_program,
                $user->class_group,
                $user->cohort_year ? 'Angkatan '.$user->cohort_year : null,
            ]);
            $scopeDescription = count($parts) > 0 ? implode(' • ', $parts) : 'Mahasiswa Binaan Saya';
        }

        $stats = [
            'is_timdis' => $isTimdis,
            'scope_description' => $scopeDescription,
            'total_participants' => $totalParticipants,
            'total_cards' => $totalMappedCards,
            'total_schedules' => $totalSchedules,
            'session_hadir' => $sessionHadir,
            'session_terlambat' => $sessionTerlambat,
            'session_izin' => $sessionIzin,
            'session_sakit' => $sessionSakit,
            'session_total' => $sessionTotal,
            'session_tidak_hadir' => $sessionTidakHadir,
            'session_belum_hadir' => $sessionTidakHadir,
            'attendance_rate' => $attendanceRate,
            'hadir_rate' => $hadirRate,
            'terlambat_rate' => $terlambatRate,
            'izin_rate' => $izinRate,
            'sakit_rate' => $sakitRate,
            'tidak_hadir_rate' => $tidakHadirRate,
            'distribution' => [
                'hadir_pct' => $hadirRate,
                'terlambat_pct' => $terlambatRate,
                'izin_pct' => $izinRate,
                'sakit_pct' => $sakitRate,
                'izin_sakit_pct' => round($izinRate + $sakitRate, 1),
                'tidak_hadir_pct' => $tidakHadirRate,
            ],
            'pending_permissions' => (Schema::hasTable('pengajuan_izin') || Schema::hasTable('permission_requests')) ? PermissionRequest::forUser($user)->where('status', 'menunggu')->count() : 0,
            'pending_izin' => (Schema::hasTable('pengajuan_izin') || Schema::hasTable('permission_requests')) ? PermissionRequest::forUser($user)->where('status', 'menunggu')->where('type', 'izin')->count() : 0,
            'pending_sakit' => (Schema::hasTable('pengajuan_izin') || Schema::hasTable('permission_requests')) ? PermissionRequest::forUser($user)->where('status', 'menunggu')->where('type', 'sakit')->count() : 0,
            'total_admins' => User::where('role', 'admin')->count(),
            'total_timdis' => User::whereIn('role', ['timdis', 'petugas'])->count(),
        ];

        // ==========================================
        // PERHITUNGAN TREN KEHADIRAN (7, 30, 90 HARI)
        // ==========================================
        $trendDays = (int) $request->input('trend_days', 7);
        if (! in_array($trendDays, [7, 30, 90])) {
            $trendDays = 7;
        }

        // Tanggal patokan: jadwal terpilih atau hari ini
        $anchorDate = Carbon::today();
        if ($selectedSchedule && Carbon::parse($selectedSchedule->schedule_date)->gt($anchorDate)) {
            $anchorDate = Carbon::parse($selectedSchedule->schedule_date);
        }

        $trendEndDate = $anchorDate->copy();
        $trendStartDate = $trendEndDate->copy()->subDays($trendDays - 1);

        // Periode perbandingan sebelumnya (durasi sama langsung sebelum periode ini)
        $prevTrendEndDate = $trendStartDate->copy()->subDays(1);
        $prevTrendStartDate = $prevTrendEndDate->copy()->subDays($trendDays - 1);

        // Ambil jadwal apel dalam periode berjalan
        $periodSchedules = ApelSchedule::whereBetween('schedule_date', [
            $trendStartDate->format('Y-m-d'),
            $trendEndDate->format('Y-m-d'),
        ])
            ->with(['attendanceRecords' => function ($q) use ($user) {
                $q->forUser($user);
            }])
            ->orderBy('schedule_date', 'asc')
            ->get();

        $schedulesByDate = $periodSchedules->groupBy(function ($sched) {
            return Carbon::parse($sched->schedule_date)->format('Y-m-d');
        });

        // Ambil jadwal periode sebelumnya untuk komparasi poin
        $prevPeriodSchedules = ApelSchedule::whereBetween('schedule_date', [
            $prevTrendStartDate->format('Y-m-d'),
            $prevTrendEndDate->format('Y-m-d'),
        ])
            ->with(['attendanceRecords' => function ($q) use ($user) {
                $q->forUser($user);
            }])
            ->get();

        $prevRates = [];
        foreach ($prevPeriodSchedules as $ps) {
            $attended = $ps->attendanceRecords->whereIn('status', ['hadir', 'terlambat'])->count();
            $prevRates[] = $totalParticipants > 0 ? min(100.0, round(($attended / $totalParticipants) * 100, 1)) : 0.0;
        }
        $prevAvgRate = count($prevRates) > 0 ? round(array_sum($prevRates) / count($prevRates), 1) : 0.0;

        // Bangun data poin tren harian
        $rawPoints = [];
        $lastKnownRate = null;
        $activeRates = [];

        for ($d = 0; $d < $trendDays; $d++) {
            $currDate = $trendStartDate->copy()->addDays($d);
            $currDateStr = $currDate->format('Y-m-d');

            if ($schedulesByDate->has($currDateStr)) {
                $daySchedules = $schedulesByDate->get($currDateStr);
                $attended = $daySchedules->flatMap->attendanceRecords->whereIn('status', ['hadir', 'terlambat'])->count();
                $dayRate = $totalParticipants > 0 ? min(100.0, round(($attended / $totalParticipants) * 100, 1)) : 0.0;
                $lastKnownRate = $dayRate;
                $activeRates[] = $dayRate;

                $rawPoints[] = [
                    'date' => $currDate,
                    'date_str' => $currDateStr,
                    'date_short' => $currDate->translatedFormat('d M'),
                    'date_full' => $currDate->translatedFormat('l, d F Y'),
                    'rate' => $dayRate,
                    'attended' => $attended,
                    'total' => $totalParticipants,
                    'has_schedule' => true,
                ];
            } else {
                // Hari tanpa jadwal apel (misal akhir pekan / libur): pertahankan tingkat kehadiran terakhir
                $dayRate = $lastKnownRate ?? ($stats['attendance_rate'] > 0 ? $stats['attendance_rate'] : 0.0);
                $rawPoints[] = [
                    'date' => $currDate,
                    'date_str' => $currDateStr,
                    'date_short' => $currDate->translatedFormat('d M'),
                    'date_full' => $currDate->translatedFormat('l, d F Y'),
                    'rate' => $dayRate,
                    'attended' => 0,
                    'total' => $totalParticipants,
                    'has_schedule' => false,
                ];
            }
        }

        // Rata-rata periode berjalan
        $trendAvgRate = count($activeRates) > 0
            ? round(array_sum($activeRates) / count($activeRates), 1)
            : ($lastKnownRate ?? $stats['attendance_rate']);
        $diffPoin = round($trendAvgRate - $prevAvgRate, 1);

        // Skala Y adaptif untuk estetika chart
        $minRateInPeriod = count($rawPoints) > 0 ? min(array_column($rawPoints, 'rate')) : 0;
        if ($minRateInPeriod >= 80) {
            $yMinScale = 80;
            $yMidScale = 90;
            $yMaxScale = 100;
        } elseif ($minRateInPeriod >= 50) {
            $yMinScale = 50;
            $yMidScale = 75;
            $yMaxScale = 100;
        } else {
            $yMinScale = 0;
            $yMidScale = 50;
            $yMaxScale = 100;
        }

        $yRange = max(1, $yMaxScale - $yMinScale);
        $totalPts = count($rawPoints);

        // Hitung koordinat SVG (ViewBox: 0 0 650 150)
        // Area koordinat grafik: X dari 40 ke 635 (lebar 595), Y dari 25 (100%) ke 125 (0%)
        $chartPoints = [];
        foreach ($rawPoints as $idx => $pt) {
            $x = round(40 + ($idx / max(1, $totalPts - 1)) * 595, 1);
            $y = round(125 - (($pt['rate'] - $yMinScale) / $yRange) * 100, 1);
            $y = max(20.0, min(128.0, $y));

            $chartPoints[] = array_merge($pt, [
                'x' => $x,
                'y' => $y,
            ]);
        }

        // Buat string SVG Path
        $linePath = '';
        if (count($chartPoints) > 0) {
            $linePath = 'M '.$chartPoints[0]['x'].' '.$chartPoints[0]['y'];
            for ($i = 1; $i < count($chartPoints); $i++) {
                $linePath .= ' L '.$chartPoints[$i]['x'].' '.$chartPoints[$i]['y'];
            }
        }
        $areaPath = count($chartPoints) > 0
            ? $linePath.' L '.$chartPoints[count($chartPoints) - 1]['x'].' 130 L '.$chartPoints[0]['x'].' 130 Z'
            : '';

        // Tentukan label sumbu X agar rapi dan tidak tumpang tindih
        $axisLabels = [];
        if ($trendDays == 7) {
            foreach ($chartPoints as $idx => $cp) {
                $axisLabels[] = [
                    'x' => $cp['x'],
                    'text' => $cp['date_short'],
                    'is_last' => $idx === $totalPts - 1,
                ];
            }
        } else {
            // Untuk 30 dan 90 hari, ambil 6 label berjarak seimbang
            $step = ($totalPts - 1) / 5;
            for ($k = 0; $k < 6; $k++) {
                $idx = min($totalPts - 1, (int) round($k * $step));
                $cp = $chartPoints[$idx];
                $axisLabels[] = [
                    'x' => $cp['x'],
                    'text' => $cp['date_short'],
                    'is_last' => $k === 5,
                ];
            }
        }

        // Titik dan Tooltip pill terakhir
        $lastPt = count($chartPoints) > 0 ? end($chartPoints) : ['x' => 615, 'y' => 70, 'rate' => $trendAvgRate];
        $pillX = max(40.0, min(590.0, $lastPt['x'] - 21.0));
        $pillY = max(8.0, $lastPt['y'] - 24.0);

        $trendData = [
            'days' => $trendDays,
            'avg_rate' => $trendAvgRate,
            'diff_poin' => $diffPoin,
            'is_positive' => $diffPoin >= 0,
            'points' => $chartPoints,
            'line_path' => $linePath,
            'area_path' => $areaPath,
            'axis_labels' => $axisLabels,
            'y_labels' => [
                'top' => "{$yMaxScale}%",
                'mid' => "{$yMidScale}%",
                'bottom' => "{$yMinScale}%",
            ],
            'latest_point' => [
                'x' => $lastPt['x'],
                'y' => $lastPt['y'],
                'rate' => $lastPt['rate'],
                'pill_x' => $pillX,
                'pill_y' => $pillY,
                'text_x' => $pillX + 21.0,
                'text_y' => $pillY + 12.5,
            ],
        ];

        // Recent Permission Requests for Dashboard preview
        $recentPermissions = (Schema::hasTable('pengajuan_izin') || Schema::hasTable('permission_requests'))
            ? PermissionRequest::forUser($user)->with(['participant', 'decider'])->latest()->take(4)->get()
            : collect();

        // Upcoming and recent schedules (order by schedule_date asc dari hari ini atau terdekat)
        $upcomingSchedules = ApelSchedule::withCount(['attendanceRecords' => function ($q) use ($user) {
            $q->forUser($user);
        }])
            ->orderBy('schedule_date', 'desc')
            ->take(5)
            ->get();

        // Log scan terakhir
        $recentEvents = AttendanceEvent::forUser($user)->with(['participant', 'schedule'])
            ->orderBy('id', 'desc')
            ->limit(6)
            ->get();

        // Breakdown per program studi untuk grafik/tabel ringkasan
        $studyProgramBreakdown = Participant::forUser($user)->active()
            ->select('study_program', DB::raw('count(*) as total'))
            ->groupBy('study_program')
            ->get();

        $allSchedules = ApelSchedule::orderBy('schedule_date', 'desc')->limit(10)->get();

        return view('pages.dashboard', compact(
            'stats',
            'trendDays',
            'trendData',
            'activeSchedule',
            'selectedSchedule',
            'recentRecords',
            'recentEvents',
            'recentPermissions',
            'upcomingSchedules',
            'studyProgramBreakdown',
            'allSchedules'
        ));
    }
}
