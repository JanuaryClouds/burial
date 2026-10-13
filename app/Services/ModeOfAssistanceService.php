<?php

namespace App\Services;

use App\Models\ModeOfAssistance;

class ModeOfAssistanceService
{
    public function store(array $data)
    {
        return ModeOfAssistance::create([
            'name' => $data['name'],
        ]);
    }
}
