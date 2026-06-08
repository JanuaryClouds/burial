<?php

namespace App\Services;

use App\Models\Sex;

class SexService
{
    /**
     * Summary of storeSex
     */
    public function storeSex(array $data): Sex
    {
        return Sex::create($data);
    }

    /**
     * Summary of updateSex
     */
    public function updateSex(array $data, Sex $sex): Sex
    {
        $sex->update($data);

        return $sex;
    }

    /**
     * Summary of deleteSex
     */
    public function deleteSex(Sex $sex): bool
    {
        return $sex->delete();
    }
}
