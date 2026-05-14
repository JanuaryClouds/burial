<?php

namespace App\Services;

use App\Models\Religion;

class ReligionService
{
    /**
     * Summary of storeReligion
     * @param array $data data to store
     * @return Religion
     */
    public function storeReligion(array $data): Religion
    {
        return Religion::create($data);
    }

    /**
     * Summary of updateReligion
     * @param array $data data to update
     * @param mixed $religion model to update
     * @return Religion updated model
     */
    public function updateReligion(array $data, $religion): Religion
    {
        return $religion->update($data);
    }

    /**
     * Summary of deleteReligion
     * @param mixed $religion model to delete
     * @return void
     */
    public function deleteReligion($religion): void
    {
        $religion->delete();
    }
}
