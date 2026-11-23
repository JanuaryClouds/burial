<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class WalkInIntake extends Model
{
    use HasUuid;

    protected $fillable = ['encoded_by', 'data'];

    protected $hidden = ['data', 'documents', 'claim_code_hash'];

    protected $casts = [
        'data' => 'encrypted:array',
        'documents' => 'encrypted:array',
        'claim_expires_at' => 'datetime',
        'claimed_at' => 'datetime',
    ];

    /** @return BelongsTo<User, $this> */
    public function encoder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'encoded_by');
    }

    /** @return BelongsTo<Application, $this> */
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'application_uuid', 'uuid');
    }

    /** @return HasOne<Client, $this> */
    public function client(): HasOne
    {
        return $this->hasOne(Client::class, 'walk_in_intake_uuid', 'uuid');
    }
}
