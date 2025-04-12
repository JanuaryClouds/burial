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
        return $assistance->update($data) ? $assistance : null;
    }

    public function deleteAssistance($assistance): Assistance
    {
        return $assistance->delete() ? $assistance : null;
    }
}