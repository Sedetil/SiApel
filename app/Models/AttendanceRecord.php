<?php

namespace App\Models;

use App\Models\Concerns\MapsLegacyColumns;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceRecord extends Model
{
    use HasFactory, MapsLegacyColumns;

    protected array $legacyColumnAliases = [
        'id' => 'id_presensi',
        'apel_schedule_id' => 'id_apel',
        'participant_id' => 'id_mahasiswa',
        'check_in_time' => 'waktu_scan',
    ];

    protected $table = 'presensi';

    protected $primaryKey = 'id_presensi';

    protected $fillable = [
        'id_apel',
        'apel_schedule_id',
        'id_mahasiswa',
        'participant_id',
        'rfid_card_id',
        'waktu_scan',
        'check_in_time',
        'check_out_time',
        'metode',
        'status',
        'menit_terlambat',
        'recorded_by',
        'device_identifier',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'waktu_scan' => 'datetime',
            'check_in_time' => 'datetime',
            'check_out_time' => 'datetime',
            'menit_terlambat' => 'integer',
        ];
    }

    public function getCheckInTimeAttribute(): ?Carbon
    {
        return isset($this->attributes['waktu_scan']) ? Carbon::parse($this->attributes['waktu_scan']) : null;
    }

    public function setCheckInTimeAttribute($value): void
    {
        $this->attributes['waktu_scan'] = $value;
    }

    public function getApelScheduleIdAttribute(): ?int
    {
        return isset($this->attributes['id_apel']) ? (int) $this->attributes['id_apel'] : null;
    }

    public function setApelScheduleIdAttribute($value): void
    {
        $this->attributes['id_apel'] = $value;
    }

    public function getParticipantIdAttribute(): ?int
    {
        return isset($this->attributes['id_mahasiswa']) ? (int) $this->attributes['id_mahasiswa'] : null;
    }

    public function setParticipantIdAttribute($value): void
    {
        $this->attributes['id_mahasiswa'] = $value;
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(ApelSchedule::class, 'id_apel');
    }

    /**
     * Relasi Diagram: Presensi (0..*) --- dicatat pada ---> (1) Apel
     */
    public function apel(): BelongsTo
    {
        return $this->schedule();
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'id_mahasiswa');
    }

    /**
     * Relasi Diagram: Mahasiswa (1) --- melakukan ---> (0..*) Presensi
     */
    public function mahasiswa(): BelongsTo
    {
        return $this->participant();
    }

    public function rfidCard(): BelongsTo
    {
        return $this->belongsTo(RfidCard::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function isHadir(): bool
    {
        return $this->status === 'hadir';
    }

    public function isTerlambat(): bool
    {
        return $this->status === 'terlambat';
    }

    /**
     * Operasi Class Diagram: hitungKeterlambatan(): int
     */
    public function hitungKeterlambatan(): int
    {
        if ($this->menit_terlambat > 0) {
            return $this->menit_terlambat;
        }

        if (! $this->check_in_time || ! $this->schedule) {
            return 0;
        }

        $threshold = Carbon::parse($this->schedule->schedule_date->format('Y-m-d').' '.$this->schedule->late_threshold_time);
        if ($this->check_in_time->lte($threshold)) {
            return 0;
        }

        return (int) ceil($threshold->diffInMinutes($this->check_in_time));
    }

    /**
     * Operasi Class Diagram: catatPresensi()
     */
    public static function catatPresensi(array $attributes): self
    {
        return self::create($attributes);
    }

    public function getIdAttribute(): int
    {
        return (int) ($this->attributes['id_presensi'] ?? $this->attributes['id'] ?? 0);
    }

    // Accessor Penyelarasan Atribut Class Diagram (Presensi)
    public function getIdPresensiAttribute(): int
    {
        return (int) ($this->attributes['id_presensi'] ?? $this->attributes['id'] ?? 0);
    }

    public function getWaktuScanAttribute(): ?Carbon
    {
        return $this->check_in_time;
    }
}
