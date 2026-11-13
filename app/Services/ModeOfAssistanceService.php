<?php

namespace App\Services;

use App\Models\ModeOfAssistance;
use App\Services\Auth\AccountAccess;

class ModeOfAssistanceService
{
    public function store(array $data)
    {
        app(AccountAccess::class)->assertWritable();
        abort_unless(auth()->user()?->isSystemAdministrator(), 403);

        return ModeOfAssistance::create([
            'name' => $data['name'],
        ]);
    }

    public function update(array $data, ModeOfAssistance $modeOfAssistance)
    {
        app(AccountAccess::class)->assertWritable();
        abort_unless(auth()->user()?->isSystemAdministrator(), 403);

        return $modeOfAssistance->update([
            'name' => $data['name'],
        ]);
    }
}
