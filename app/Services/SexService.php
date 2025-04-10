<?php

namespace App\Services;

use App\Models\Sex;

class SexService 
{
    public function storeSex(array $data): Sex
    {
        return Sex::create($data);
    }

    public function updateSex(array $data, $sex): Sex
    {
        if ($sex->update($data)) { 
            return $sex;
        }
        return null;
    }

    public function deleteSex($sex): Sex
    {
        if($sex->delete())
        {
            return $sex;
        }
        return null;
    }
}