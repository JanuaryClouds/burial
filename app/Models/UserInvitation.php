<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property array<int, string> $roles
 * @property Carbon $expires_at
 * @property Carbon|null $accepted_at
 * @property Carbon|null $revoked_at
 */
class UserInvitation extends Model
{
    protected $guarded = [];

    protected $hidden = ['token', 'token_hash'];

    protected function casts(): array
    {
        return ['roles' => 'array', 'token' => 'encrypted', 'expires_at' => 'datetime',
            'accepted_at' => 'datetime', 'revoked_at' => 'datetime'];
    }

    public function isPending(): bool
    {
        return $this->accepted_at === null && $this->revoked_at === null;
    }

    public function status(): string
    {
        return $this->accepted_at ? 'Accepted' : ($this->revoked_at ? 'Revoked' : ($this->expires_at->lte(now()) ? 'Expired' : 'Pending'));
    }

    public function managementVersion(): string
    {
        return hash('sha256', json_encode([$this->status(), $this->roles, $this->expires_at->toIso8601String(),
            $this->accepted_at?->toIso8601String(), $this->revoked_at?->toIso8601String()]));
    }
}
