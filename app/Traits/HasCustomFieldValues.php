<?php

namespace App\Traits;

use App\Models\CustomFieldValue;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasCustomFieldValues
{
    /**
     * Summary of customValues
     */
    public function customValues(): MorphMany
    {
        return $this->morphMany(CustomFieldValue::class, 'valuable');
    }
}
