<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Str;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 */
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
        return $this->first_name.' '.
            ($this->middle_name ? Str::limit($this->middle_name, 1, '.').' ' : '').
            $this->last_name.
            ($this->suffix ? ' '.$this->suffix : '');
    }

    /**
     * Summary of clients
     * @return HasMany<Client>
     */
    public function clients(): HasMany
    {
        return $this->hasMany(Client::class, 'user_id', 'id');
    }

    /**
     * Summary of processLogs
     * @return HasMany<ProcessLog>
     */
    public function processLogs(): HasMany
    {
        return $this->hasMany(ProcessLog::class, 'added_by', 'id');
    }

    /**
     * Summary of encoder
     * @return HasMany<BurialAssistance>
     */
    public function encoder(): HasMany
    {
        return $this->hasMany(BurialAssistance::class, 'encoder', 'id');
    }

    /**
     * Summary of initialChecker
     * @return HasMany<BurialAssistance>
     */
    public function initialChecker(): HasMany
    {
        return $this->hasMany(BurialAssistance::class, 'initial_checker', 'id');
    }

    /**
     * Summary of assignedTo
     * @return HasMany<BurialAssistance>
     */
    public function assignedTo(): HasMany
    {
        return $this->hasMany(BurialAssistance::class, 'assigned_to', 'id');
    }

    /**
     * Summary of claimantChange
     * @return HasMany<ClaimantChange>
     */
    public function claimantChange(): HasMany
    {
        return $this->hasMany(ClaimantChange::class, 'new_claimant_user_id', 'id');
    }
}
