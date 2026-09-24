<?php

namespace App\Models;

use App\Traits\HasRemarks;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Referral extends Model
{
    use HasFactory, HasRemarks, HasUuid;

    protected $table = 'referrals';

    protected $fillable = [
        'application_uuid',
        'referred_to',
        'reason',
    ];

    protected $casts = [
        'referred_to' => 'encrypted',
        'reason' => 'encrypted',
    ];

    /**
     * Summary of application
     *
     * @return BelongsTo<Application, Referral>
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'application_uuid', 'uuid');
    }
}
