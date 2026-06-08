<?php

namespace App\Services;

use App\Models\CivilStatus;

class CivilStatusService
{
    /**
     * Summary of storeCivilStatus
     * @param array $data
     * @return CivilStatus
     */
    public function storeCivilStatus(array $data): CivilStatus
    {
        return CivilStatus::create($data);
    }

    /**
     * Summary of updateCivilStatus
     * @param array $data
     * @param CivilStatus $civil
     * @return CivilStatus
     */
    public function updateCivilStatus(array $data, CivilStatus $civil): CivilStatus
    {
        $civil->update($data);

        return $civil->fresh();
    }

    /**
     * Summary of deleteCivilStatus
     * @param CivilStatus $civil
     * @return bool
     */
    public function deleteCivilStatus(CivilStatus $civil): bool
    {
        return $civil->delete();
    }
}
