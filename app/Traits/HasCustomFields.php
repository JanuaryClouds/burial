<?php

namespace App\Traits;

use App\Models\CustomField;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasCustomFields
{
    /**
     * Summary of customFields
     * @return MorphMany
     */
    public function customFields(): MorphMany
    {
        return $this->morphMany(CustomField::class, 'fieldable');
    }
}
