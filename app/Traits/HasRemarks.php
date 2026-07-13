<?php

namespace App\Traits;

use App\Models\Remark;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasRemarks
{
    /**
     * Summary of remarks
     * @return MorphMany
     */
    public function remarks(): MorphMany
    {
        return $this->morphMany(Remark::class, 'remarkable');
    }
}
