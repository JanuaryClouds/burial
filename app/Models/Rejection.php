<?php

namespace App\Models;

use App\Traits\HasUuid;
use Database\Factories\RejectionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rejection extends Model
{
    /** @use HasFactory<RejectionFactory> */
    use HasFactory, HasUuid;

    protected $fillable = [
        'application_uuid',
        'reason',
        'rejected_by',
    ];

    protected $casts = [
        'reason' => 'encrypted',
    ];

    /*
    |--------------------------------------------------------------------------
    | Model Relationships
    |--------------------------------------------------------------------------
    |
    | Relationships for the Rejection model
    |
    */

    /**
     * Summary of application
     *
     * @return BelongsTo<Application, Rejection>
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'application_uuid', 'uuid');
    }

    /**
     * Summary of rejectedBy
     *
     * @return BelongsTo<User, Rejection>
     */
    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by', 'id');
    }

    /*
    |--------------------------------------------------------------------------
    | Model Scopes
    |--------------------------------------------------------------------------
    |
    | Scopes for the Rejection model
    |
    */
}
