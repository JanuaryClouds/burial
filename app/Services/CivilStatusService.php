<?php

namespace App\Services;

use App\Models\CivilStatus;

class CivilStatusService
{
    /**
     * Summary of storeCivilStatus
     * @param array $data information to store
     * @return CivilStatus
     */
    public function storeCivilStatus(array $data): CivilStatus
    {
        return CivilStatus::create($data);
    }

    /**
     * Summary of updateCivilStatus
     * @param array $data data to update
     * @param mixed $civil model to update
     * @return CivilStatus updated model
     */
    public function updateCivilStatus(array $data, $civil): CivilStatus
    {
        return $civil->update($data);
    }

    /**
     * Summary of deleteCivilStatus
     * @param mixed $civil model to delete
     * @return void
     */
    public function deleteCivilStatus($civil): void
    {
        $civil->delete();
    }
}
