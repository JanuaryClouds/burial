<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentTemplate extends Model
{
    use HasUuid;

    protected $fillable = ['name', 'path', 'original_filename'];

    /** @return BelongsTo<FuneralAssistanceType, $this> */
    public function funeralAssistanceType(): BelongsTo
    {
        return $this->belongsTo(FuneralAssistanceType::class, 'funeral_assistance_type_uuid', 'uuid');
    }
}
