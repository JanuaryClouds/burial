<?php

namespace App\Services;

use App\Models\ModeOfAssistance;

class ModeOfAssistanceService
{
    public function storeModeOfAssistance(array $data): ModeOfAssistance
    {
        return ModeOfAssistance::create($data);
    }

    public function updateModeOfAssistance(array $data, $moa): ModeOfAssistance
    {
        if($moa->update($data))
        {
            return $moa;
        }
        return null;
    }

    public function deleteModeOfAssistance($moa): ModeOfAssistance
    {
        if($moa->delete())
        {
            return $moa;
        }
        return null;
    }
}