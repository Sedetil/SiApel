<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApelSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'category',
        'schedule_date',
        'start_time',
        'end_time',
        'attendance_start',
        'attendance_end',
        'late_threshold_time',
        'location',
        'status',
        'created_by',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'schedule_date' => 'date',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    public function attendanceEvents(): HasMany
    {
        return $this->hasMany(AttendanceEvent::class);
    }

    /**
     * Cek apakah waktu saat ini berada dalam periode pembukaan absensi
     */
    public function isWithinAttendanceWindow(?Carbon $now = null): bool
    {
        $now = $now ?? Carbon::now();
        $dateStr = $this->schedule_date->format('Y-m-d');

        $windowStart = Carbon::parse("{$dateStr} {$this->attendance_start}");
        $windowEnd = Carbon::parse("{$dateStr} {$this->attendance_end}");

        return $now->betweenIncluded($windowStart, $windowEnd);
    }

    /**
     * Tentukan apakah waktu tap dianggap tepat waktu atau terlambat
     */
    public function determineStatus(?Carbon $tapTime = null): string
    {
        $tapTime = $tapTime ?? Carbon::now();
        $dateStr = $this->schedule_date->format('Y-m-d');
        $threshold = Carbon::parse("{$dateStr} {$this->late_threshold_time}");

        // Aturan FR-05: Jika tapTime <= late_threshold_time maka hadir, jika setelahnya maka terlambat
        return $tapTime->lte($threshold) ? 'hadir' : 'terlambat';
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('schedule_date', Carbon::today());
    }
}
