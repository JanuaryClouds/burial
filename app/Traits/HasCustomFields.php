<?php

namespace App\Traits;

use App\Models\CustomField;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasCustomFields
{
    /**
     * @return MorphMany<CustomField, $this>
     */
    public function customFields(): MorphMany
    {
        return $this->morphMany(CustomField::class, 'fieldable')->orderBy('order_no');
    }
}
