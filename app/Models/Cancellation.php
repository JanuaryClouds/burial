<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cancellation extends Model
{
    /** @use HasFactory<\Database\Factories\CancellationFactory> */
    use HasUuid, HasFactory;

    protected $fillable = [
        'application_uuid',
        'reason',
        'cancelled_by',
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
     * @return BelongsTo<Application, Cancellation>
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'application_uuid', 'uuid');
    }

    /**
     * Summary of cancelledBy
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
