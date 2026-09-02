<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

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

    /**
     * Summary of fullname
     *
     * @return string returns the fullname of the user
     */
    public function fullname(): string
    {
        return $this->first_name.' '.
            ($this->middle_name ? Str::limit($this->middle_name, 1, '.').' ' : '').
            $this->last_name.
            ($this->suffix ? ' '.$this->suffix : '');
    }

    /**
     * Summary of clients
     *
     * @return HasMany<Client>
     */
    public function clients(): HasMany
    {
        return $this->hasMany(Client::class, 'user_id', 'id');
    }

    /**
     * Summary of claimantChange
     *
     * @return HasMany<ClaimantChange>
     */
    public function claimantChange(): HasMany
    {
        return $this->hasMany(ClaimantChange::class, 'new_claimant_user_id', 'id');
    }

    /**
     * Summary of beneficiary
     *
     * @return HasMany<Beneficiary, User>
     */
    public function beneficiaries(): HasMany
    {
        return $this->hasMany(Beneficiary::class, 'created_by', 'id');
    }

    /**
     * Summary of rejections
     *
     * @return HasMany<Rejection, User>
     */
    public function rejections(): HasMany
    {
        return $this->hasMany(Rejection::class, 'rejected_by', 'id');
    }

    /**
     * Summary of cancellations
     *
     * @return HasMany<Cancellation, User>
     */
    public function cancellations(): HasMany
    {
        return $this->hasMany(Cancellation::class, 'cancelled_by', 'id');
    }
}
