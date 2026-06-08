<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cheque extends Model
{
    use HasFactory;

    protected $table = 'cheques';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'burial_assistance_id',
        'claimant_id',
        'obr_number',
        'cheque_number',
        'dv_number',
        'amount',
        'date_issued',
        'date_claimed',
        'status',
    ];

    // TODO Soon to send to disbursement system
    // TODO Deletion is not allowed, but should be stored as request for editing and deleting
    // TODO Head can see those request

    /**
     * Summary of burialAssistance
     *
     * @return BelongsTo<BurialAssistance, Cheque>
     */
    public function burialAssistance(): BelongsTo
    {
        return $this->belongsTo(BurialAssistance::class, 'burial_assistance_id', 'id');
    }

    /**
     * Summary of claimant
     *
     * @return BelongsTo<Claimant, Cheque>
     */
    public function claimant(): BelongsTo
    {
        return $this->belongsTo(Claimant::class, 'claimant_id', 'id');
    }
}
