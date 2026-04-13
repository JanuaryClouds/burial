<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Referral;
use Illuminate\Support\Str;

class ReferralService
{
    public function store(array $data, $client_id)
    {
        $client = Client::findOrFail($client_id);
        return Referral::create([
            'id' => Str::uuid(),
            'client_id' => $client->id,
            'beneficiary_id' => $client->beneficiary?->id,
            'referral_to' => $data['referral_to'],
            'remarks' => $data['remarks'],
        ]);
    }

    public function update(array $data)
    {
        //
    }
}