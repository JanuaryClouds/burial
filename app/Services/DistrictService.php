<?php

namespace App\Services;

use App\Models\District;

class DistrictService
{
    /**
     * Summary of storeDistrict
     * @param array $data
     * @return District
     */
    public function storeDistrict(array $data): District
    {
        return District::create($data);
    }

    /**
     * Summary of updateDistrict
     * @param array $data
     * @param District $district
     * @return District
     */
    public function updateDistrict(array $data, District $district): District
    {
        $district->update($data);

        return $district;
    }

    /**
     * Summary of deleteDistrict
     * @param District $district
     * @return bool
     */
    public function deleteDistrict(District $district): bool
    {
        return $district->delete();
    }
}
