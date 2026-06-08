<?php

namespace App\Services;

use App\Models\District;

class DistrictService
{
    /**
     * Summary of storeDistrict
     */
    public function storeDistrict(array $data): District
    {
        return District::create($data);
    }

    /**
     * Summary of updateDistrict
     */
    public function updateDistrict(array $data, District $district): District
    {
        $district->update($data);

        return $district;
    }

    /**
     * Summary of deleteDistrict
     */
    public function deleteDistrict(District $district): bool
    {
        return $district->delete();
    }
}
