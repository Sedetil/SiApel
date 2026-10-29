<?php

namespace App\Models;

use App\Models\Concerns\MapsLegacyColumns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class EkuliahSyncLog extends Model
{
    use HasFactory, MapsLegacyColumns;

    protected array $legacyColumnAliases = [
        'id' => 'id_sinkron',
        'records_synced' => 'jumlah_data',
        'synced_by' => 'id_admin',
    ];

    protected $table = 'log_sinkronisasi';

    protected $primaryKey = 'id_sinkron';

    protected $fillable = [
        'sync_type',
        'status',
        'jumlah_data',
        'records_synced',
        'message',
        'details',
        'id_admin',
        'synced_by',
        'waktu',
    ];

    protected function casts(): array
    {
        return [
            'details' => 'array',
            'jumlah_data' => 'integer',
            'records_synced' => 'integer',
            'waktu' => 'datetime',
        ];
    }

    public function getRecordsSyncedAttribute(): int
    {
        return (int) ($this->attributes['jumlah_data'] ?? 0);
    }

    public function setRecordsSyncedAttribute($value): void
    {
        $this->attributes['jumlah_data'] = $value;
    }

    public function getSyncedByAttribute(): ?int
    {
        return isset($this->attributes['id_admin']) ? (int) $this->attributes['id_admin'] : null;
    }

    public function setSyncedByAttribute($value): void
    {
        $this->attributes['id_admin'] = $value;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_admin');
    }

    /**
     * Alias relasi diagram: Admin (1) --- menjalankan ---> (0..*) LogSinkronisasi
     */
    public function admin(): BelongsTo
    {
        return $this->user();
    }

    public function getIdAttribute(): int
    {
        return (int) ($this->attributes['id_sinkron'] ?? $this->attributes['id'] ?? 0);
    }

    /**
     * Accessor Class Diagram: id_sinkron
     */
    public function getIdSinkronAttribute(): int
    {
        return (int) ($this->attributes['id_sinkron'] ?? $this->attributes['id'] ?? 0);
    }

    /**
     * Accessor Class Diagram: waktu
     */
    public function getWaktuAttribute(): ?Carbon
    {
        return $this->created_at;
    }

    /**
     * Accessor Class Diagram: jumlah_data
     */
    public function getJumlahDataAttribute(): int
    {
        return (int) ($this->records_synced ?? 0);
    }
}
