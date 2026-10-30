<?php

namespace App\Models;

use App\Models\Concerns\MapsLegacyColumns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RfidCard extends Model
{
    use HasFactory, MapsLegacyColumns;

    protected array $legacyColumnAliases = [
        'participant_id' => 'id_mahasiswa',
    ];

    protected $fillable = [
        'id_mahasiswa',
        'participant_id',
        'card_uid',
        'is_active',
        'issued_at',
        'deactivated_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'issued_at' => 'datetime',
            'deactivated_at' => 'datetime',
        ];
    }

    public function getParticipantIdAttribute(): ?int
    {
        return isset($this->attributes['id_mahasiswa']) ? (int) $this->attributes['id_mahasiswa'] : null;
    }

    public function setParticipantIdAttribute($value): void
    {
        $this->attributes['id_mahasiswa'] = $value;
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'id_mahasiswa');
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->participant();
    }

    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
