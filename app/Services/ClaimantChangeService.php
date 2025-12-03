<?php

namespace App\Services;

use App\Models\ClaimantChange;

class ClaimantChangeService
{
    public function store(array $data): ClaimantChange
    {
        return ClaimantChange::create($data);
    }
}