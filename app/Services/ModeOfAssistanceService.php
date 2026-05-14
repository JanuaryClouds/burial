<?php

namespace App\Services;

use App\Models\ModeOfAssistance;

class ModeOfAssistanceService
{
    /**
     * Summary of storeModeOfAssistance
     * @param array $data data to store
     * @return ModeOfAssistance
     */
    public function storeModeOfAssistance(array $data): ModeOfAssistance
    {
        return ModeOfAssistance::create($data);
    }

    /**
     * Summary of updateModeOfAssistance
     * @param array $data data to update
     * @param mixed $moa model to update
     * @return ModeOfAssistance updated model
     */
    public function updateModeOfAssistance(array $data, $moa): ModeOfAssistance
    {
        return $moa->update($data);
    }

    /**
     * Summary of deleteModeOfAssistance
     * @param mixed $moa model to delete
     * @return void
     */
    public function deleteModeOfAssistance($moa): void
    {
        $moa->delete();
    }
}
