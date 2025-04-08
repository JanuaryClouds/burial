<?php

namespace App\Services;

use App\Models\Assistance;

class AssistanceService
{
    public function storeAssistance(array $data): Assistance
    {
        return Assistance::create($data);
    }

    public function updateAssistance(array $data, $assistance): void
    {
        $assistance->update($data);
    }

    public function deleteAssistance($assistance): void
    {
        $assistance->delete();
    }
}