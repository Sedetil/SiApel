<?php

namespace App\Services;

use App\Models\ApelSchedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ApelScheduleAutomationService
{
    /**
     * Memastikan jadwal apel untuk hari Senin & Jumat otomatis tersedia.
     * Dapat dipanggil saat aplikasi diakses (Dashboard/Monitor/Schedule) atau via Task Scheduler.
     *
     * @param int $weeksAhead Jumlah minggu ke depan yang di-generate (default 4 minggu)
     * @return Collection Daftar jadwal yang dibuat atau sudah ada
     */
    public static function ensureMondayAndFridaySchedules(int $weeksAhead = 4): Collection
    {
        $today = Carbon::today();
        $adminId = User::where('role', 'admin')->value('id') ?? 1;

        // Ambil hari Senin dari minggu saat ini (atau hari ini jika sedang Senin)
        $currentMonday = $today->copy()->startOfWeek(Carbon::MONDAY);
        $endDate = $today->copy()->addWeeks($weeksAhead)->endOfWeek(Carbon::SUNDAY);

        $processedSchedules = collect();

        $cursor = $currentMonday->copy();
        while ($cursor->lte($endDate)) {
            // Cek apakah cursor adalah hari Senin atau Jumat
            if ($cursor->isMonday() || $cursor->isFriday()) {
                $scheduleDateStr = $cursor->format('Y-m-d');
                $dayName = $cursor->isoFormat('dddd'); // Senin atau Jumat

                $title = "Apel Kedisiplinan {$dayName} Kampus POLINELA";

                // Tentukan status default:
                // Jika tanggal jadwal adalah hari ini -> 'active' (jika belum ada yang aktif)
                // Jika tanggal jadwal di masa lalu -> 'completed'
                // Jika tanggal jadwal di masa depan -> 'scheduled'
                $defaultStatus = 'scheduled';
                if ($cursor->isToday()) {
                    $hasActive = ApelSchedule::where('status', 'active')
                        ->where('id', '!=', function ($q) use ($scheduleDateStr) {
                            $q->select('id')->from('apel_schedules')->whereDate('schedule_date', $scheduleDateStr);
                        })->exists();

                    $defaultStatus = $hasActive ? 'scheduled' : 'active';
                } elseif ($cursor->lt($today)) {
                    $defaultStatus = 'completed';
                }

                $schedule = ApelSchedule::firstOrCreate(
                    [
                        'schedule_date' => $scheduleDateStr,
                        'category' => 'rutin',
                    ],
                    [
                        'title' => $title,
                        'start_time' => '07:00:00',
                        'end_time' => '08:00:00',
                        'attendance_start' => '06:30:00',
                        'attendance_end' => '08:00:00',
                        'late_threshold_time' => '07:15:00',
                        'location' => 'Lapangan Apel Utama POLINELA',
                        'status' => $defaultStatus,
                        'created_by' => $adminId,
                        'description' => "Jadwal apel kedisiplinan rutin hari {$dayName} Politeknik Negeri Lampung.",
                    ]
                );

                $processedSchedules->push($schedule);
            }

            $cursor->addDay();
        }

        return $processedSchedules;
    }
}
