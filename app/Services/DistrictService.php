<?php

namespace App\Services;

use App\Models\District;

class DistrictService
{
    public function storeDistrict(array $data): District
    {
        return District::create($data);
    }

    public function updateDistrict(array $data, $district): District
    {
        if($district->update($data))
        {
            return $district;
        }
        return null;
    }

    public function deleteDistrict($district): District
    {
        if($district->delete())
        {
            return $district;
        }
        return null;
    }
}