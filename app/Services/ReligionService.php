<?php

namespace App\Services;

use App\Models\Religion;

class ReligionService
{
    public function storeReligion(array $data): Religion
    {
        return Religion::create($data);
    }

    public function updateReligion(array $data, $religion): Religion
    {
        if ($religion->update($data)) {
            return $religion;
        }

        return null;
    }

    public function deleteReligion($religion): Religion
    {
        if ($religion->delete()) {
            return $religion;
        }

        return null;
    }
}
