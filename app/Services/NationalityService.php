<?php

namespace App\Services;

use App\Models\Nationality;

class NationalityService
{
    public function storeNationality(array $data): Nationality
    {
        return Nationality::create($data);
    }

    public function updateNationality(array $data, $nationality): Nationality
    {
        if ($nationality->update($data)) {
            return $nationality;
        }

        return null;
    }

    public function deleteNationality($nationality): Nationality
    {
        if ($nationality->delete()) {
            return $nationality;
        }

        return null;
    }
}
