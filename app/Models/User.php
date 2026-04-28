<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Str;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    protected $fillable = [
        'citizen_uuid',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'email',
        'contact_number',
        'password',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'first_name' => 'encrypted',
        'middle_name' => 'encrypted',
        'last_name' => 'encrypted',
        'suffix' => 'encrypted',
        'contact_number' => 'encrypted',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    public function fullname()
    {
        if (! $this) {
            return '';
        }

        return $this->first_name.' '.
            ($this->middle_name ? Str::limit($this->middle_name, 1, '.').' ' : '').
            $this->last_name.
            ($this->suffix ? ' '.$this->suffix : '');
    }

    public function clients()
    {
        return $this->hasMany(Client::class, 'user_id', 'id');
    }

    public function processLogs()
    {
        return $this->hasMany(ProcessLog::class, 'added_by', 'id');
    }

    public function encoder()
    {
        return $this->hasMany(BurialAssistance::class, 'encoder', 'id');
    }

    public function initialChecker()
    {
        return $this->hasMany(BurialAssistance::class, 'initial_checker', 'id');
    }

    public function assignedTo()
    {
        return $this->hasMany(BurialAssistance::class, 'assigned_to', 'id');
    }

    public function claimantChange()
    {
        return $this->hasMany(ClaimantChange::class, 'new_claimant_user_id', 'id');
    }
}
