<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $fillable = [
        'nom', 'prenom', 'email', 'telephone', 'photo', 'is_active', 'last_login_at', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
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

    public function getNameAttribute(): string
    {
        return trim(($this->prenom ?? '') . ' ' . ($this->nom ?? ''));
    }

    public function prospects() { return $this->hasMany(Prospect::class, 'commercial_id'); }
    public function relances() { return $this->hasMany(Relance::class, 'commercial_id'); }
    public function tasks() { return $this->hasMany(Task::class); }
    public function logs() { return $this->hasMany(ActivityLog::class); }
    public function objectifs() { return $this->hasMany(Objectif::class, 'commercial_id'); }

    public static function getAssignableUsers()
    {
        $user = auth()->user();
        if ($user->hasRole(['Administrateur', 'Directeur Général'])) {
            return self::all();
        }
        if ($user->hasRole('Responsable Commercial')) {
            return self::whereDoesntHave('roles', function($q) {
                $q->whereIn('name', ['Administrateur', 'Directeur Général']);
            })->get();
        }
        return self::where('id', $user->id)->get();
    }
}