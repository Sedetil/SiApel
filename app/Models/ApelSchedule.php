<?php

namespace App\Models;

use App\Models\Concerns\MapsLegacyColumns;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApelSchedule extends Model
{
    use HasFactory, MapsLegacyColumns;

    protected array $legacyColumnAliases = [
        'id' => 'id_apel',
        'schedule_date' => 'tanggal',
        'start_time' => 'jam_mulai',
        'end_time' => 'jam_selesai',
        'category' => 'jenis',
    ];

    protected $table = 'apel';

    protected $primaryKey = 'id_apel';

    protected $fillable = [
        'title',
        'jenis',
        'category',
        'tanggal',
        'schedule_date',
        'jam_mulai',
        'start_time',
        'jam_selesai',
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
            'tanggal' => 'date',
            'schedule_date' => 'date',
        ];
    }

    public function getScheduleDateAttribute(): ?Carbon
    {
        return isset($this->attributes['tanggal']) ? Carbon::parse($this->attributes['tanggal']) : null;
    }

    public function setScheduleDateAttribute($value): void
    {
        $this->attributes['tanggal'] = $value;
    }

    public function getStartTimeAttribute(): ?string
    {
        return $this->attributes['jam_mulai'] ?? null;
    }

    public function setStartTimeAttribute($value): void
    {
        $this->attributes['jam_mulai'] = $value;
    }

    public function getEndTimeAttribute(): ?string
    {
        return $this->attributes['jam_selesai'] ?? null;
    }

    public function setEndTimeAttribute($value): void
    {
        $this->attributes['jam_selesai'] = $value;
    }

    public function getCategoryAttribute(): ?string
    {
        return $this->attributes['jenis'] ?? null;
    }

    public function setCategoryAttribute($value): void
    {
        $this->attributes['jenis'] = $value;
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi Diagram: Admin (1) --- mengelola ---> (0..*) Apel
     */
    public function admin(): BelongsTo
    {
        return $this->creator();
    }

    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class, 'id_apel');
    }

    /**
     * Relasi Diagram: Presensi (0..*) --- dicatat pada ---> (1) Apel
     */
    public function presensi(): HasMany
    {
        return $this->attendanceRecords();
    }

    /**
     * Relasi Diagram: Apel (1) --- untuk ---> (0..*) PengajuanIzin
     */
    public function pengajuanIzin(): HasMany
    {
        return $this->hasMany(PermissionRequest::class, 'id_apel');
    }

    public function attendanceEvents(): HasMany
    {
        return $this->hasMany(AttendanceEvent::class, 'id_apel');
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

    /**
     * Hitung durasi keterlambatan dalam menit relatif terhadap late_threshold_time
     */
    public function hitungMenitKeterlambatan(?Carbon $tapTime = null): int
    {
        $tapTime = $tapTime ?? Carbon::now();
        $dateStr = $this->schedule_date->format('Y-m-d');
        $threshold = Carbon::parse("{$dateStr} {$this->late_threshold_time}");

        if ($tapTime->lte($threshold)) {
            return 0;
        }

        return (int) ceil($threshold->diffInMinutes($tapTime));
    }

    /**
     * Operasi Class Diagram: ubahApel()
     */
    public function ubahApel(array $data): void
    {
        $this->update($data);
    }

    /**
     * Operasi Class Diagram: batalkanApel()
     */
    public function batalkanApel(string $alasan = 'Dibatalkan'): void
    {
        $this->update([
            'status' => 'cancelled',
            'description' => $this->description ? ($this->description." [Catatan Pembatalan: {$alasan}]") : "Catatan Pembatalan: {$alasan}",
        ]);
    }

    /**
     * Operasi Class Diagram: generateJadwalRutin()
     */
    public static function generateJadwalRutin(array $attributes = []): self
    {
        $defaultDate = Carbon::today();
        $defaults = [
            'title' => 'Apel Rutin Kedisiplinan',
            'category' => 'rutin',
            'schedule_date' => $defaultDate,
            'start_time' => '07:00:00',
            'end_time' => '08:00:00',
            'attendance_start' => '06:30:00',
            'attendance_end' => '07:30:00',
            'late_threshold_time' => '07:15:00',
            'location' => 'Lapangan Apel POLINELA',
            'status' => 'scheduled',
        ];

        return self::create(array_merge($defaults, $attributes));
    }

    public function getIdAttribute(): int
    {
        return (int) ($this->attributes['id_apel'] ?? $this->attributes['id'] ?? 0);
    }

    // Accessor Penyelarasan Atribut Class Diagram (Apel)
    public function getIdApelAttribute(): int
    {
        return (int) ($this->attributes['id_apel'] ?? $this->attributes['id'] ?? 0);
    }

    public function getTanggalAttribute(): Carbon
    {
        return isset($this->attributes['tanggal']) ? Carbon::parse($this->attributes['tanggal']) : Carbon::today();
    }

    public function getJamMulaiAttribute(): string
    {
        return $this->attributes['jam_mulai'] ?? '00:00:00';
    }

    public function getJamSelesaiAttribute(): string
    {
        return $this->attributes['jam_selesai'] ?? '00:00:00';
    }

    public function getJenisAttribute(): string
    {
        return $this->attributes['jenis'] ?? '';
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('tanggal', Carbon::today());
    }
}
