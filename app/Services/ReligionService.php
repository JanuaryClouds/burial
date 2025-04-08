<?php

namespace App\Services;

use App\Models\Religion;

class ReligionService
{
    public function storeReligion(array $data): Religion
    {
        return Religion::create($data);
    }

    public function updateReligion(array $data, $religion): void
    {
        $religion->update($data);
    }

    public function deleteReligion($religion): void
    {
        $religion->delete();
    }
}