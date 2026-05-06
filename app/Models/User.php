<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; 

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = ['role_id', 'name', 'email', 'password', 'foto'];

    public function role() {
        return $this->belongsTo(Role::class);
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function hasRole(string $roleSlug): bool
    {
        if ($this->role === null) return false;
        // Normalisasi dash vs underscore agar konsisten
        $normalize = fn($s) => str_replace('-', '_', strtolower($s));
        return $normalize($this->role->slug) === $normalize($roleSlug);
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }
}