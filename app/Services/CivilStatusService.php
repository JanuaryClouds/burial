<?php

namespace App\Services;

use App\Models\CivilStatus;

class CivilStatusService
{
    public function storeCivilStatus(array $data): CivilStatus
    {
        return CivilStatus::create($data);
    }

    public function updateCivilStatus(array $data, $civil): void
    {
        $civil->update($data);
    }

    public function deleteCivilStatus($civil): void
    {
        $civil->delete();
    }
}