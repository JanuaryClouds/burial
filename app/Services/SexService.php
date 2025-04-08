<?php

namespace App\Services;

use App\Models\Sex;

class SexService 
{
    public function storeSex(array $data): Sex
    {
        return Sex::create($data);
    }

    public function updateSex(array $data, $sex): void
    {
        $sex->update($data);
    }

    public function deleteSex($sex): void
    {
        $sex->delete();
    }
}