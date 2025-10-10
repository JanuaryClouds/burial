<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'contact_number',
        'password',
    ];
    
    protected $hidden = [
        'password',
        'remember_token',
    ];
    
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function isAdmin ()
    {
        return $this->hasRole('admin');
    }

    public function processLogs() {
        return $this->hasMany(ProcessLog::class, 'added_by', 'id');
    }

    public function encoder() {
        return $this->hasMany(BurialAssistance::class, 'encoder', 'id');
    }

    public function initialChecker() {
        return $this->hasMany(BurialAssistance::class, 'initial_checker', 'id');
    }

    public function assignedTo() {
        return $this->hasMany(BurialAssistance::class, 'assigned_to', 'id');
    }

    public function routeRestrictions() {
        return $this->hasMany(UserRouteRestriction::class, 'user_id', 'id');
    }
}