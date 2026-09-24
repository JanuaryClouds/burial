<?php

namespace App\Models;

use App\Traits\HasUuid;
use Database\Factories\CancellationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cancellation extends Model
{
    /** @use HasFactory<CancellationFactory> */
    use HasFactory, HasUuid;

    protected $fillable = [
        'application_uuid',
        'reason',
        'cancelled_by',
    ];

    protected $casts = [
        'reason' => 'encrypted',
    ];

    /*
    |--------------------------------------------------------------------------
    | Model Relationships
    |--------------------------------------------------------------------------
    |
    | Relationships for the Cancellation model
    |
    */

    /**
     * Summary of application
     *
     * @return BelongsTo<Application, Cancellation>
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'application_uuid', 'uuid');
    }

    /**
     * Summary of cancelledBy
     *
     * @return BelongsTo<User, Cancellation>
     */
    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by', 'id');
    }

    /*
    |--------------------------------------------------------------------------
    | Model Scopes
    |--------------------------------------------------------------------------
    |
    | Scopes for the Cancellation model
    |
    */
}
