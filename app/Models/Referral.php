<?php

namespace App\Models;

use App\Traits\HasRemarks;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Referral extends Model
{
    use HasFactory, HasUuid, HasRemarks;

    protected $table = 'referrals';

    protected $fillable = [
        'application_uuid',
        'referral_to',
    ];

    /**
     * Summary of client
     *
     * @return BelongsTo<Client, Referral>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Summary of beneficiary
     *
     * @return BelongsTo<Beneficiary, Referral>
     */
    public function beneficiary(): BelongsTo
    {
        return $this->belongsTo(Beneficiary::class);
    }
}
