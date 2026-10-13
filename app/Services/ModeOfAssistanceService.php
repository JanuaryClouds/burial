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

    public function update(array $data, ModeOfAssistance $modeOfAssistance)
    {
        return $modeOfAssistance->update([
            'name' => $data['name'],
        ]);
    }
}
