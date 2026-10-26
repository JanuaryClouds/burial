<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApplicationClaimantAssignment extends Model
{
    use HasUuid;

    protected $guarded = [];

    protected $casts = [
        'identity_snapshot' => 'encrypted:array',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::updating(function (self $assignment) {
            if ($assignment->isDirty(array_diff(array_keys($assignment->getAttributes()), ['ended_at', 'updated_at']))) {
                throw new \LogicException('Claimant assignments are immutable audit records.');
            }
        });
        static::deleting(fn () => throw new \LogicException('Claimant assignment history must be preserved.'));
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'application_uuid', 'uuid');
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function relationship(): BelongsTo
    {
        return $this->belongsTo(Relationship::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_uuid', 'uuid');
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class, 'claimant_assignment_uuid', 'uuid');
    }

    public function interviews(): HasMany
    {
        return $this->hasMany(Interview::class, 'claimant_assignment_uuid', 'uuid');
    }

    public function recommendations(): HasMany
    {
        return $this->hasMany(Recommendation::class, 'claimant_assignment_uuid', 'uuid');
    }

    public static function snapshot(Client $client): array
    {
        $client->loadMissing(['user', 'demographic', 'socialInfo', 'address']);

        return [
            'user_id' => $client->user_id,
            'name' => $client->fullname(),
            'email' => $client->user?->email,
            'date_of_birth' => $client->date_of_birth,
            'contact_number' => $client->contact_number,
            'address' => $client->address?->only(['region_name', 'province_name', 'municipality_name', 'barangay_name', 'street', 'house_number']),
            'demographic' => $client->demographic?->only(['sex_id', 'nationality_id', 'religion_id']),
            'social_info' => $client->socialInfo?->only(['civil_id', 'education_id', 'philhealth', 'skill', 'income']),
        ];
    }
}
