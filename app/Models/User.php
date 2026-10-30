<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'study_program',
        'phone',
        'avatar',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
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
}
