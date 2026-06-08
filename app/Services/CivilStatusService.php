<?php

namespace App\Services;

use App\Models\CivilStatus;

class CivilStatusService
{
    /**
     * Summary of storeCivilStatus
     */
    public function storeCivilStatus(array $data): CivilStatus
    {
        return CivilStatus::create($data);
    }

    /**
     * Summary of updateCivilStatus
     */
    public function updateCivilStatus(array $data, CivilStatus $civil): CivilStatus
    {
        $civil->update($data);

        return $civil->fresh();
    }

    /**
     * Summary of deleteCivilStatus
     */
    public function deleteCivilStatus(CivilStatus $civil): bool
    {
        return $civil->delete();
    }
}
