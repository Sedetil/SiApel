<?php

namespace App\Models;

use App\Models\Concerns\MapsLegacyColumns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Request;

class AuditLog extends Model
{
    use HasFactory, MapsLegacyColumns;

    protected array $legacyColumnAliases = [
        'user_id' => 'id_pengguna',
    ];

    public $timestamps = false;

    protected $fillable = [
        'id_pengguna',
        'user_id',
        'action',
        'entity_type',
        'entity_id',
        'description',
        'ip_address',
        'user_agent',
        'payload',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function getUserIdAttribute(): ?int
    {
        return isset($this->attributes['id_pengguna']) ? (int) $this->attributes['id_pengguna'] : null;
    }

    public function setUserIdAttribute($value): void
    {
        $this->attributes['id_pengguna'] = $value;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_pengguna');
    }

    public function pengguna(): BelongsTo
    {
        return $this->user();
    }

    /**
     * Helper cepat untuk mencatat audit log
     */
    public static function log(string $action, string $description, ?string $entityType = null, ?int $entityId = null, ?array $payload = null, ?int $userId = null): self
    {
        return self::create([
            'user_id' => $userId ?? auth()->id(),
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'description' => $description,
            'ip_address' => Request::ip(),
            'user_agent' => substr(Request::userAgent() ?? '', 0, 255),
            'payload' => $payload,
            'created_at' => now(),
        ]);
    }
}
