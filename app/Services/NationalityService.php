<?php

namespace App\Services;

use App\Models\Nationality;

class NationalityService
{
    public function storeNationality(array $data): Nationality
    {
        return Nationality::create($data);
    }

    public function updateNationality(array $data, $nationality): void
    {
        $nationality->update($data);
    }

    public function deleteNationality($nationality): void
    {
        $nationality->delete();
    }
}