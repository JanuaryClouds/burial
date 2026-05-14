<?php

namespace App\Services;

use App\Models\Nationality;

class NationalityService
{
    /**
     * Summary of storeNationality
     * @param array $data data to store
     * @return Nationality
     */
    public function storeNationality(array $data): Nationality
    {
        return Nationality::create($data);
    }

    /**
     * Summary of updateNationality
     * @param array $data data to update
     * @param mixed $nationality model to update
     * @return Nationality updated model
     */
    public function updateNationality(array $data, $nationality): Nationality
    {
        return $nationality->update($data);
    }

    /**
     * Summary of deleteNationality
     * @param mixed $nationality model to delete
     * @return void
     */
    public function deleteNationality($nationality): void
    {
        $nationality->delete();
    }
}
