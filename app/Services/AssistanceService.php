<?php

namespace App\Services;

use App\Models\Assistance;

class AssistanceService
{
    public function storeAssistance(array $data): Assistance
    {
        return Assistance::create($data);
    }

    public function updateAssistance(array $data, $assistance): Assistance
    {
        if($assistance->update($data))
        {
            return $assistance;
        }
        return null;
    }

    public function deleteAssistance($assistance): Assistance
    {
        if($assistance->delete())
        {
            return $assistance;
        }
        return null;
    }
}