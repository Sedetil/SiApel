<?php

namespace App\Models;

use App\Models\Concerns\MapsLegacyColumns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceEvent extends Model
{
    use HasFactory, MapsLegacyColumns;

    protected array $legacyColumnAliases = [
        'apel_schedule_id' => 'id_apel',
        'participant_id' => 'id_mahasiswa',
    ];

    public $timestamps = false;

    protected $fillable = [
        'id_apel',
        'apel_schedule_id',
        'card_uid',
        'id_mahasiswa',
        'participant_id',
        'scan_type',
        'scanned_at',
        'result',
        'status_determined',
        'rejection_reason',
        'device_identifier',
        'raw_payload',
    ];

    protected function casts(): array
    {
        return [
            'scanned_at' => 'datetime',
            'raw_payload' => 'array',
        ];
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

    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'id_mahasiswa');
    }
}
