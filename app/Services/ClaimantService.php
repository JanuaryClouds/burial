<?php

namespace App\Services;

use App\Models\Claimant;

class ClaimantService
{
    public function store(array $data): Claimant
    {
        return Claimant::create($data);
    }
}
