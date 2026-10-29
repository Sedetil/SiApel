<?php

namespace App\Models;

use App\Models\Concerns\MapsLegacyColumns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PermissionRequest extends Model
{
    use HasFactory, MapsLegacyColumns;

    protected array $legacyColumnAliases = [
        'id' => 'id_izin',
        'participant_id' => 'id_mahasiswa',
        'apel_schedule_id' => 'id_apel',
        'decided_by' => 'id_timdis',
        'type' => 'jenis',
        'reason' => 'alasan',
        'attachment_path' => 'berkas_bukti',
    ];

    protected $table = 'pengajuan_izin';

    protected $primaryKey = 'id_izin';

    protected $fillable = [
        'id_mahasiswa',
        'participant_id',
        'id_apel',
        'apel_schedule_id',
        'jenis',
        'type',
        'alasan',
        'reason',
        'berkas_bukti',
        'attachment_path',
        'status',
        'id_timdis',
        'decided_by',
        'decided_at',
        'notes',
        'alasan_penolakan',
    ];

    protected $casts = [
        'decided_at' => 'datetime',
    ];

    public function getTypeAttribute(): string
    {
        return $this->attributes['jenis'] ?? '';
    }

    public function setTypeAttribute($value): void
    {
        $this->attributes['jenis'] = $value;
    }

    public function getReasonAttribute(): string
    {
        return $this->attributes['alasan'] ?? '';
    }

    public function setReasonAttribute($value): void
    {
        $this->attributes['alasan'] = $value;
    }

    public function getAttachmentPathAttribute(): ?string
    {
        return $this->attributes['berkas_bukti'] ?? null;
    }

    public function setAttachmentPathAttribute($value): void
    {
        $this->attributes['berkas_bukti'] = $value;
    }

    public function getParticipantIdAttribute(): ?int
    {
        return isset($this->attributes['id_mahasiswa']) ? (int) $this->attributes['id_mahasiswa'] : null;
    }

    public function setParticipantIdAttribute($value): void
    {
        $this->attributes['id_mahasiswa'] = $value;
    }

    public function getApelScheduleIdAttribute(): ?int
    {
        return isset($this->attributes['id_apel']) ? (int) $this->attributes['id_apel'] : null;
    }

    public function setApelScheduleIdAttribute($value): void
    {
        $this->attributes['id_apel'] = $value;
    }

    public function getDecidedByAttribute(): ?int
    {
        return isset($this->attributes['id_timdis']) ? (int) $this->attributes['id_timdis'] : null;
    }

    public function setDecidedByAttribute($value): void
    {
        $this->attributes['id_timdis'] = $value;
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'id_mahasiswa');
    }

    /**
     * Relasi Diagram: Mahasiswa (1) --- mengajukan ---> (0..*) PengajuanIzin
     */
    public function mahasiswa(): BelongsTo
    {
        return $this->participant();
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(ApelSchedule::class, 'id_apel');
    }

    /**
     * Relasi Diagram: Apel (1) --- untuk ---> (0..*) PengajuanIzin
     */
    public function apel(): BelongsTo
    {
        return $this->schedule();
    }

    public function decider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_timdis');
    }

    /**
     * Relasi Diagram: Timdis (1) --- memvalidasi ---> (0..*) PengajuanIzin
     */
    public function timdis(): BelongsTo
    {
        return $this->decider();
    }

    public function isPending(): bool
    {
        return $this->status === 'menunggu';
    }

    public function isApproved(): bool
    {
        return $this->status === 'disetujui';
    }

    public function isRejected(): bool
    {
        return $this->status === 'ditolak';
    }

    /**
     * Operasi Class Diagram: terima(): void
     */
    public function terima(?string $notes = null, ?int $decidedBy = null): void
    {
        $decidedBy = $decidedBy ?? auth()->id();

        $this->update([
            'status' => 'disetujui',
            'decided_by' => $decidedBy,
            'decided_at' => now(),
            'notes' => $notes ?? 'Pengajuan izin disetujui.',
        ]);

        if ($this->apel_schedule_id) {
            AttendanceRecord::updateOrCreate(
                [
                    'apel_schedule_id' => $this->apel_schedule_id,
                    'participant_id' => $this->participant_id,
                ],
                [
                    'status' => $this->type === 'sakit' ? 'sakit' : 'izin',
                    'notes' => 'Disetujui dari pengajuan: '.$this->reason,
                    'recorded_by' => $decidedBy,
                ]
            );
        }
    }

    /**
     * Operasi Class Diagram: tolak(alasan): void
     */
    public function tolak(string $alasan, ?int $decidedBy = null): void
    {
        $decidedBy = $decidedBy ?? auth()->id();

        $this->update([
            'status' => 'ditolak',
            'alasan_penolakan' => $alasan,
            'decided_by' => $decidedBy,
            'decided_at' => now(),
            'notes' => $alasan,
        ]);
    }

    public function getIdAttribute(): int
    {
        return (int) ($this->attributes['id_izin'] ?? $this->attributes['id'] ?? 0);
    }

    // Accessor Penyelarasan Atribut Class Diagram (PengajuanIzin)
    public function getIdIzinAttribute(): int
    {
        return (int) ($this->attributes['id_izin'] ?? $this->attributes['id'] ?? 0);
    }

    public function getJenisAttribute(): string
    {
        return $this->type;
    }

    public function getAlasanAttribute(): string
    {
        return $this->reason;
    }

    public function getBerkasBuktiAttribute(): ?string
    {
        return $this->attachment_path;
    }
}
