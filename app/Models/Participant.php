<?php

namespace App\Models;

use App\Models\Concerns\MapsLegacyColumns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Participant extends Model
{
    use HasFactory, MapsLegacyColumns, SoftDeletes;

    protected array $legacyColumnAliases = [
        'id' => 'id_mahasiswa',
        'identifier' => 'npm',
        'nim' => 'npm',
        'npm' => 'npm',
        'name' => 'nama',
        'class_group' => 'kelas',
    ];

    protected $table = 'mahasiswa';

    protected $primaryKey = 'id_mahasiswa';

    protected $fillable = [
        'npm',
        'nim',
        'identifier',
        'nama',
        'name',
        'study_program',
        'kelas',
        'class_group',
        'semester',
        'kode_qr',
        'id_rfid',
        'gender',
        'phone',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'semester' => 'integer',
            'id_rfid' => 'integer',
        ];
    }

    public function getNpmAttribute(): string
    {
        return $this->attributes['npm'] ?? $this->attributes['nim'] ?? '';
    }

    public function setNpmAttribute($value): void
    {
        $this->attributes['npm'] = $value;
    }

    public function getIdentifierAttribute(): string
    {
        return $this->npm;
    }

    public function setIdentifierAttribute($value): void
    {
        $this->npm = $value;
    }

    public function getNameAttribute(): string
    {
        return $this->attributes['nama'] ?? '';
    }

    public function setNameAttribute($value): void
    {
        $this->attributes['nama'] = $value;
    }

    public function getClassGroupAttribute(): ?string
    {
        return $this->attributes['kelas'] ?? null;
    }

    public function setClassGroupAttribute($value): void
    {
        $this->attributes['kelas'] = $value;
    }

    public function rfidCards(): HasMany
    {
        return $this->hasMany(RfidCard::class, 'id_mahasiswa');
    }

    public function activeRfidCard(): HasOne
    {
        return $this->hasOne(RfidCard::class, 'id_mahasiswa')->where('is_active', true);
    }

    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class, 'id_mahasiswa');
    }

    /**
     * Relasi Diagram: Mahasiswa (1) --- melakukan ---> (0..*) Presensi
     */
    public function presensi(): HasMany
    {
        return $this->attendanceRecords();
    }

    public function permissionRequests(): HasMany
    {
        return $this->hasMany(PermissionRequest::class, 'id_mahasiswa');
    }

    /**
     * Relasi Diagram: Mahasiswa (1) --- mengajukan ---> (0..*) PengajuanIzin
     */
    public function pengajuanIzin(): HasMany
    {
        return $this->permissionRequests();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    /**
     * Operasi Class Diagram: melakukanPresensi()
     */
    public function melakukanPresensi(int $apelScheduleId, string $metode = 'RFID'): AttendanceRecord
    {
        $schedule = ApelSchedule::findOrFail($apelScheduleId);
        $now = now();
        $status = $schedule->determineStatus($now);
        $menitTerlambat = $schedule->hitungMenitKeterlambatan($now);

        return AttendanceRecord::updateOrCreate(
            [
                'apel_schedule_id' => $schedule->id,
                'participant_id' => $this->id,
            ],
            [
                'rfid_card_id' => $this->activeRfidCard?->id,
                'check_in_time' => $now,
                'metode' => $metode,
                'status' => $status,
                'menit_terlambat' => $menitTerlambat,
            ]
        );
    }

    /**
     * Operasi Class Diagram: lihatRiwayatKehadiran(): list<String>
     */
    public function lihatRiwayatKehadiran(): array
    {
        return $this->attendanceRecords()
            ->with('schedule')
            ->orderBy('check_in_time', 'desc')
            ->get()
            ->map(function ($record) {
                $tanggal = $record->schedule?->schedule_date?->format('d/m/Y') ?? '-';
                $waktu = $record->check_in_time ? $record->check_in_time->format('H:i') : '-';

                return "[{$tanggal}] {$record->schedule?->title}: {$record->status} ({$waktu})";
            })
            ->toArray();
    }

    /**
     * Operasi Class Diagram: ajukanIzin(): list<String>
     */
    public function ajukanIzin(string $alasan, string $jenis = 'izin', ?int $apelScheduleId = null, ?string $berkasBukti = null): PermissionRequest
    {
        return PermissionRequest::create([
            'participant_id' => $this->id,
            'apel_schedule_id' => $apelScheduleId,
            'type' => $jenis,
            'reason' => $alasan,
            'attachment_path' => $berkasBukti,
            'status' => 'menunggu',
        ]);
    }

    /**
     * Operasi Class Diagram: unduhKartuQR(): string
     */
    public function unduhKartuQR(): string
    {
        return $this->kode_qr ?? 'QR_'.strtoupper(md5($this->identifier));
    }

    /**
     * Operasi Class Diagram: cetakKartuRFID(): string
     */
    public function cetakKartuRFID(): string
    {
        return $this->activeRfidCard?->card_uid ?? 'BELUM_TERDAFTAR';
    }

    public function getIdAttribute(): int
    {
        return (int) ($this->attributes['id_mahasiswa'] ?? $this->attributes['id'] ?? 0);
    }

    public function getIdMahasiswaAttribute(): int
    {
        return (int) ($this->attributes['id_mahasiswa'] ?? $this->attributes['id'] ?? 0);
    }

    // Accessor & Mutator Penyelarasan Atribut Mahasiswa (NPM & Kompatibilitas NIM)
    public function getNimAttribute(): string
    {
        return $this->npm;
    }

    public function setNimAttribute($value): void
    {
        $this->npm = $value;
    }

    public function getKelasAttribute(): ?string
    {
        return $this->attributes['kelas'] ?? null;
    }

    public function getIdRfidAttribute(): ?int
    {
        return isset($this->attributes['id_rfid']) ? (int) $this->attributes['id_rfid'] : $this->activeRfidCard?->id;
    }
}
