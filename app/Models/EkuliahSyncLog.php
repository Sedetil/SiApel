<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EkuliahSyncLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'sync_type',
        'status',
        'records_synced',
        'message',
        'details',
        'synced_by',
    ];

    protected $casts = [
        'details' => 'array',
        'records_synced' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'synced_by');
    }
}
