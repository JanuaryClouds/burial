<?php

namespace App\Services;

use App\Models\Sex;

class SexService
{
    /**
     * Summary of storeSex
     * @param array $data
     * @return Sex
     */
    public function storeSex(array $data): Sex
    {
        return Sex::create($data);
    }

    /**
     * Summary of updateSex
     * @param array $data
     * @param Sex $sex
     * @return Sex
     */
    public function updateSex(array $data, Sex $sex): Sex
    {
        $sex->update($data);

        return $sex;
    }

    /**
     * Summary of deleteSex
     * @param Sex $sex
     * @return bool
     */
    public function deleteSex(Sex $sex): bool
    {
        return $sex->delete();
    }
}
