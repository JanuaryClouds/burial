<?php

namespace App\Traits;

use App\Models\CustomFieldValue;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasCustomFieldValues
{
    /**
     * @return MorphMany<CustomFieldValue, $this>
     */
    public function customValues(): MorphMany
    {
        return $this->morphMany(CustomFieldValue::class, 'valuable');
    }
}
