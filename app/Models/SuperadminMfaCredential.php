<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuperadminMfaCredential extends Model
{
    protected $guarded = [];

    // Never expose credential material or replay state through serialization.
    protected $hidden = ['secret', 'recovery_hashes', 'version', 'last_used_step'];

    protected static function booted(): void
    {
        static::saved(fn ($credential) => request()->attributes->remove('mfa.credential.'.$credential->user_id));
        static::deleted(fn ($credential) => request()->attributes->remove('mfa.credential.'.$credential->user_id));
    }

    protected function casts(): array
    {
        return ['secret' => 'encrypted', 'recovery_hashes' => 'array', 'confirmed_at' => 'datetime', 'last_used_step' => 'integer'];
    }
}
