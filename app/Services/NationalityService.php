<?php

namespace App\Services;

use App\Models\Nationality;

class NationalityService
{
    public function storeNationality(array $data): Nationality
    {
        return Nationality::create($data);
    }

    public function updateNationality(array $data, Nationality $nationality): void
    {
        $nationality->update($data);
    }

    public function deleteNationality(Nationality $nationality): void
    {
        $nationality->delete();
    }

    public function restoreNationality(Nationality $nationality): void
    {
        $nationality->restore();
    }
}
