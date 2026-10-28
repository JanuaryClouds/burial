<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\Contracts\HasAbilities;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    /** Web sessions have no token; Sanctum cookie sessions use a TransientToken. */
    public function currentAccessToken(): ?HasAbilities
    {
        return $this->accessToken;
    }

    public function isInternal(): bool
    {
        return $this->accessRoles()->isNotEmpty();
    }

    public function isSystemAdministrator(): bool
    {
        return $this->accessRoles()->contains('name', 'superadmin');
    }

    public function hasOfficeAccess(): bool
    {
        return $this->accessRoles()->contains(fn ($role) => in_array($role->name, ['CSWDO', 'superadmin'], true));
    }

    /** Read fresh assignments so polling and subsequent actions observe revoked access. */
    public function effectivePermissionNames(): array
    {
        $roles = $this->accessRoles();
        $roles->loadMissing('permissions');
        $this->loadMissing('permissions');

        return $this->permissions->pluck('name')->merge(
            $roles->flatMap(fn ($role) => $role->permissions->pluck('name'))
        )->unique()->values()->all();
    }

    /** @return Collection<int, Role> */
    private function accessRoles(): Collection
    {
        $key = 'burial.access_roles.'.$this->id;
        if (! request()->attributes->get($key) || ! $this->relationLoaded('roles')) {
            $this->load('roles');
            $this->unsetRelation('permissions');
            request()->attributes->set($key, true);
        }

        return $this->roles;
    }

    public function canOfficeAction(string $permission): bool
    {
        return $this->isSystemAdministrator() || ($this->hasOfficeAccess() && in_array($permission, $this->effectivePermissionNames(), true));
    }

    public function canSearchApplications(): bool
    {
        return $this->hasOfficeAccess() || ($this->isInternal() && collect($this->effectivePermissionNames())->contains(fn ($name) => str_starts_with($name, 'Workflow ')));
    }

    public function canWorkflowPermission(string $permission): bool
    {
        return $this->isSystemAdministrator() || ($this->isInternal() && in_array($permission, $this->effectivePermissionNames(), true));
    }

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
        'password_change_required',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'password_change_required' => 'boolean',
        'first_name' => 'encrypted',
        'middle_name' => 'encrypted',
        'last_name' => 'encrypted',
        'suffix' => 'encrypted',
        'contact_number' => 'encrypted',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected static function booted(): void
    {
        static::updated(function (User $user): void {
            if ($user->wasChanged('is_active') && ! $user->is_active) {
                $user->tokens()->delete();
            }
        });
    }

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

    /*
    |--------------------------------------------------------------------------
    | Model Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeIndex($query)
    {
        return $query->whereHas('roles', function ($query) {
            $query->where('name', '!=', 'superadmin');
        })
            ->orderBy('created_at', 'asc');
    }
}
