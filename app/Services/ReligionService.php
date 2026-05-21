<?php

namespace App\Services;

use App\Models\Religion;

class ReligionService
{
    public function storeReligion(array $data): Religion
    {
        return Religion::create($data);
    }

    public function updateReligion(array $data, Religion $religion): void
    {
        $religion->update($data);
    }

    public function deleteReligion(Religion $religion): void
    {
        $religion->delete();
    }

    public function restoreReligion(Religion $religion): void
    {
        $religion->restore();
    }
}
