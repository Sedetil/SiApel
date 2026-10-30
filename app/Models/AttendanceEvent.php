<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceEvent extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'apel_schedule_id',
        'card_uid',
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

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(ApelSchedule::class, 'apel_schedule_id');
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class);
    }
}
