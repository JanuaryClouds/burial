<?php

namespace App\Services;

use App\Models\ModeOfAssistance;

class ModeOfAssistanceService
{
    /**
     * Summary of storeModeOfAssistance
     * @param array $data
     * @return ModeOfAssistance
     */
    public function storeModeOfAssistance(array $data): ModeOfAssistance
    {
        return ModeOfAssistance::create($data);
    }

    /**
     * Summary of updateModeOfAssistance
     * @param array $data
     * @param ModeOfAssistance $moa
     * @return bool
     */
    public function updateModeOfAssistance(array $data, ModeOfAssistance $moa): bool
    {
        return $moa->update($data);
    }

    /**
     * Summary of deleteModeOfAssistance
     * @param ModeOfAssistance $moa
     * @return bool
     */
    public function deleteModeOfAssistance(ModeOfAssistance $moa): bool
    {
        return $moa->delete();
    }
}
