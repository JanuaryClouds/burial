<?php

namespace App\Models;

use App\Traits\HasUuid;
use Database\Factories\RemarkFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Remark extends Model
{
    /** @use HasFactory<RemarkFactory> */
    use HasFactory, HasUuid;

    protected $table = 'remarks';

    protected $fillable = [
        'content',
        'remarkable_id',
        'remarkable_type',
        'user_id',
        'parent_uuid',
    ];

    protected $casts = [
        'content' => 'encrypted',
    ];

    /*
    |--------------------------------------------------------------------------
    | Model Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Summary of parentRemark
     *
     * @return BelongsTo<Remark, Remark>
     */
    public function parentRemark(): BelongsTo
    {
        return $this->belongsTo(Remark::class, 'parent_uuid', 'uuid');
    }

    /**
     * Summary of childRemarks
     *
     * @return HasMany<Remark>
     */
    public function childRemarks(): HasMany
    {
        return $this->hasMany(Remark::class, 'parent_uuid', 'uuid');
    }

    /**
     * Summary of user
     *
     * @return BelongsTo<User, Remark>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
