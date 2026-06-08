<?php

namespace App\Services;

use App\Models\ModeOfAssistance;

class ModeOfAssistanceService
{
    /**
     * Summary of storeModeOfAssistance
     */
    public function storeModeOfAssistance(array $data): ModeOfAssistance
    {
        return ModeOfAssistance::create($data);
    }

    /**
     * Summary of updateModeOfAssistance
     */
    public function updateModeOfAssistance(array $data, ModeOfAssistance $moa): bool
    {
        return $moa->update($data);
    }

    /**
     * Summary of deleteModeOfAssistance
     */
    public function deleteModeOfAssistance(ModeOfAssistance $moa): bool
    {
        return $moa->delete();
    }
}
