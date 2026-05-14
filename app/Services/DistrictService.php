<?php

namespace App\Services;

use App\Models\District;

class DistrictService
{
    /**
     * Summary of storeDistrict
     * @param array $data information to store
     * @return District
     */
    public function storeDistrict(array $data): District
    {
        return District::create($data);
    }

    /**
     * Summary of updateDistrict
     * @param array $data information to update
     * @param mixed $district model to update
     * @return District
     */
    public function updateDistrict(array $data, $district): District
    {
        return $district->update($data);
    }

    /**
     * Summary of deleteDistrict
     * @param mixed $district model to delete
     * @return void if succesful
     */
    public function deleteDistrict($district): void
    {
        $district->delete();
    }
}
