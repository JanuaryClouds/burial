<?php

namespace App\Services;

use App\Models\Assistance;

class AssistanceService
{
    /**
     * Summary of storeAssistance
     * @param array $data information to store
     * @return Assistance
     */
    public function storeAssistance(array $data): Assistance
    {
        return Assistance::create($data);
    }

    /**
     * Summary of updateAssistance
     * @param array $data information to store
     * @param mixed $assistance model to update
     * @return Assistance updated model
     */
    public function updateAssistance(array $data, $assistance): Assistance
    {
        return $assistance->update($data);
    }

    /**
     * Summary of deleteAssistance
     * @param mixed $assistance model to delete
     * @return void if successful
     */
    public function deleteAssistance($assistance): void
    {
        $assistance->delete();
    }
}
