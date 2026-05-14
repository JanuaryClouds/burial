<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ProcessLog extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'burial_assistance_id',
        'claimant_id',
        'loggable_id',
        'loggable_type',
        'is_progress_step',
        'date_in',
        'date_out',
        'comments',
        'extra_data',
        'added_by',
    ];

    protected $casts = [
        'extra_data' => 'array',
    ];

    protected $table = 'process_logs';

    /**
     * Summary of burialAssistance
     * @return BelongsTo<BurialAssistance, ProcessLog>
     */
    public function burialAssistance(): BelongsTo
    {
        return $this->belongsTo(BurialAssistance::class, 'burial_assistance_id', 'id');
    }

    /**
     * Summary of claimant
     * @return BelongsTo<Claimant, ProcessLog>
     */
    public function claimant(): BelongsTo
    {
        return $this->belongsTo(Claimant::class, 'claimant_id', 'id');
    }

    /**
     * Summary of addedBy
     * @return BelongsTo<User, ProcessLog>
     */
    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by', 'id');
    }

    /**
     * Summary of loggable
     * @return MorphTo
     */
    public function loggable(): MorphTo
    {
        return $this->morphTo();
    }
}
