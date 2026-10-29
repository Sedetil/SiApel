<?php

namespace App\Models;

use App\Models\Concerns\MapsLegacyColumns;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, MapsLegacyColumns, Notifiable;

    protected array $legacyColumnAliases = [
        'id' => 'id_pengguna',
        'name' => 'nama',
        'password' => 'kata_sandi',
        'avatar' => 'foto_profil',
        'role' => 'peran',
        'study_program' => 'prodi_binaan',
    ];

    protected $table = 'pengguna';

    protected $primaryKey = 'id_pengguna';

    protected $fillable = [
        'nama',
        'name',
        'username',
        'email',
        'kata_sandi',
        'password',
        'peran',
        'role',
        'prodi_binaan',
        'study_program',
        'jurusan_binaan',
        'department',
        'class_group',
        'cohort_year',
        'phone',
        'foto_profil',
        'avatar',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'kata_sandi',
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function getAuthPasswordName(): string
    {
        return 'kata_sandi';
    }

    public function getAuthIdentifierName(): string
    {
        return 'id_pengguna';
    }

    public function getNameAttribute(): string
    {
        return $this->attributes['nama'] ?? '';
    }

    public function setNameAttribute($value): void
    {
        $this->attributes['nama'] = $value;
    }

    public function getPasswordAttribute(): string
    {
        return $this->attributes['kata_sandi'] ?? '';
    }

    public function setPasswordAttribute($value): void
    {
        $this->attributes['kata_sandi'] = (is_string($value) && str_starts_with($value, '$2y$')) ? $value : bcrypt($value);
    }

    public function setKataSandiAttribute($value): void
    {
        $this->attributes['kata_sandi'] = (is_string($value) && str_starts_with($value, '$2y$')) ? $value : bcrypt($value);
    }

    public function getAvatarAttribute(): ?string
    {
        return $this->attributes['foto_profil'] ?? null;
    }

    public function setAvatarAttribute($value): void
    {
        $this->attributes['foto_profil'] = $value;
    }

    public function getRoleAttribute(): string
    {
        return $this->attributes['peran'] ?? '';
    }

    public function setRoleAttribute($value): void
    {
        $this->attributes['peran'] = $value;
    }

    public function getStudyProgramAttribute(): ?string
    {
        return $this->attributes['prodi_binaan'] ?? null;
    }

    public function setStudyProgramAttribute($value): void
    {
        $this->attributes['prodi_binaan'] = $value;
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar && Storage::disk('public')->exists($this->avatar)) {
            return asset('storage/'.$this->avatar);
        }

        return 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&background=0d47a1&color=ffffff&bold=true';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isPetugas(): bool
    {
        return in_array($this->role, ['petugas', 'timdis'], true);
    }

    public function isTimdis(): bool
    {
        return in_array($this->role, ['timdis', 'petugas'], true);
    }

    public function hasRole(string|array $roles): bool
    {
        $roles = (array) $roles;

        if (in_array('timdis', $roles, true) && $this->role === 'petugas') {
            return true;
        }

        if (in_array('petugas', $roles, true) && $this->role === 'timdis') {
            return true;
        }

        return in_array($this->role, $roles, true);
    }

    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    /**
     * Operasi Class Diagram (Pengguna): editProfil()
     */
    public function editProfil(array $data): void
    {
        $this->update($data);
    }

    /**
     * Operasi Class Diagram (Pengguna): gantiKataSandi()
     */
    public function gantiKataSandi(string $newPassword): void
    {
        $this->update(['password' => bcrypt($newPassword)]);
    }

    /**
     * Operasi Class Diagram (Pengguna): unggahFotoProfil()
     */
    public function unggahFotoProfil(string $avatarPath): void
    {
        $this->update(['avatar' => $avatarPath]);
    }

    /**
     * Operasi Class Diagram (Admin): kelolaAkunTimdis()
     */
    public function kelolaAkunTimdis()
    {
        return self::whereIn('role', ['timdis', 'petugas']);
    }

    /**
     * Operasi Class Diagram (Admin): kelolaApel()
     */
    public function kelolaApel()
    {
        return ApelSchedule::query();
    }

    /**
     * Operasi Class Diagram (Admin / Timdis): lihatInformasiKehadiran(): array
     */
    public function lihatInformasiKehadiran(?int $scheduleId = null): array
    {
        $query = AttendanceRecord::query();
        if ($scheduleId) {
            $query->where('apel_schedule_id', $scheduleId);
        }

        return [
            'total_recorded' => (clone $query)->count(),
            'total_hadir' => (clone $query)->where('status', 'hadir')->count(),
            'total_terlambat' => (clone $query)->where('status', 'terlambat')->count(),
            'total_izin' => (clone $query)->where('status', 'izin')->count(),
            'total_sakit' => (clone $query)->where('status', 'sakit')->count(),
            'total_alpa' => (clone $query)->where('status', 'alpa')->count(),
        ];
    }

    /**
     * Operasi Class Diagram (Admin): jalankanSinkronisasi()
     */
    public function jalankanSinkronisasi(string $type = 'mahasiswa'): EkuliahSyncLog
    {
        $count = $type === 'kehadiran' ? AttendanceRecord::count() : Participant::count();

        return EkuliahSyncLog::create([
            'sync_type' => $type,
            'status' => 'success',
            'records_synced' => $count,
            'message' => "Sinkronisasi {$type} berhasil dijalankan.",
            'synced_by' => $this->id,
        ]);
    }

    /**
     * Operasi Class Diagram (Timdis): validasiIzin(): bool
     */
    public function validasiIzin(PermissionRequest $request, bool $approve, ?string $alasan = null): bool
    {
        if ($approve) {
            $request->terima($alasan, $this->id);
        } else {
            $request->tolak($alasan ?? 'Ditolak oleh '.$this->name, $this->id);
        }

        return true;
    }

    /**
     * Operasi Class Diagram (Timdis): eksporKehadiranExcel(): string
     */
    public function eksporKehadiranExcel(?int $scheduleId = null): string
    {
        // Menghasilkan referensi nama file ekspor kehadiran
        $dateSuffix = now()->format('Ymd_His');

        return "rekap_kehadiran_{$dateSuffix}.xlsx";
    }

    /**
     * Relasi Diagram: Admin (1) --- menjalankan ---> (0..*) LogSinkronisasi
     */
    public function logSinkronisasi(): HasMany
    {
        return $this->hasMany(EkuliahSyncLog::class, 'id_admin');
    }

    public function getIdAttribute(): int
    {
        return (int) ($this->attributes['id_pengguna'] ?? $this->attributes['id'] ?? 0);
    }

    // Accessor Penyelarasan Atribut Class Diagram (Pengguna, Admin, Timdis)
    public function getIdPenggunaAttribute(): int
    {
        return (int) ($this->attributes['id_pengguna'] ?? $this->attributes['id'] ?? 0);
    }

    public function getIdAdminAttribute(): int
    {
        return (int) ($this->attributes['id_pengguna'] ?? $this->attributes['id'] ?? 0);
    }

    public function getIdTimdisAttribute(): int
    {
        return (int) ($this->attributes['id_pengguna'] ?? $this->attributes['id'] ?? 0);
    }

    public function getNamaAttribute(): string
    {
        return $this->attributes['nama'] ?? '';
    }

    public function getFotoProfilAttribute(): string
    {
        return $this->avatar_url;
    }

    public function getPeranAttribute(): string
    {
        return $this->attributes['peran'] ?? '';
    }

    public function getProdiBinaanAttribute(): ?string
    {
        return $this->attributes['prodi_binaan'] ?? null;
    }

    /**
     * Dapatkan daftar kelas yang dipegang oleh Timdis (Mendukung Multi-Scope)
     *
     * @return array<string>
     */
    public function getAssignedClassesAttribute(): array
    {
        if (empty($this->class_group)) {
            return [];
        }

        $val = trim($this->class_group);

        // Jika format JSON array (misal: ["TRPL 1A", "TRPL 1B"])
        if (str_starts_with($val, '[') && str_ends_with($val, ']')) {
            $decoded = json_decode($val, true);
            if (is_array($decoded)) {
                return array_filter(array_map('trim', $decoded));
            }
        }

        // Jika format dipisah koma (misal: "TRPL 1A, TRPL 1B")
        if (str_contains($val, ',')) {
            return array_filter(array_map('trim', explode(',', $val)));
        }

        return [$val];
    }
}
