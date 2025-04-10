<?php

namespace App\Services;

use App\Models\CivilStatus;

class CivilStatusService
{
    public function storeCivilStatus(array $data): CivilStatus
    {
        return CivilStatus::create($data);
    }

    public function updateCivilStatus(array $data, $civil): CivilStatus
    {
        if($civil->update($data))
        {
            return $civil;
        }
        return null;
    }

    public function deleteCivilStatus($civil): CivilStatus
    {
        if($civil->delete())
        {
            return $civil;
        }
        return null;
    }
}