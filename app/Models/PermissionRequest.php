<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PermissionRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'participant_id',
        'apel_schedule_id',
        'type',
        'reason',
        'attachment_path',
        'status',
        'decided_by',
        'decided_at',
        'notes',
    ];

    protected $casts = [
        'decided_at' => 'datetime',
    ];

    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(ApelSchedule::class, 'apel_schedule_id');
    }

    public function decider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by');
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
}
