<?php

namespace App\Services;

use App\Models\Sex;

class SexService
{
    /**
     * Summary of storeSex
     * @param array $data data to store
     * @return Sex
     */
    public function storeSex(array $data): Sex
    {
        return Sex::create($data);
    }

    /**
     * Summary of updateSex
     * @param array $data data to update
     * @param mixed $sex model to update
     * @return Sex updated model
     */
    public function updateSex(array $data, $sex): Sex
    {
        return $sex->update($data);
    }

    /**
     * Summary of deleteSex
     * @param mixed $sex model to delete
     * @return void
     */
    public function deleteSex($sex): void
    {
        $sex->delete();
    }
}
