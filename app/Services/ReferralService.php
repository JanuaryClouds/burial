<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Referral;
use Illuminate\Support\Str;

class ReferralService
{
    public function index(?string $user_id = null)
    {
        return Referral::with('client.user')
            ->when($user_id, function ($query) use ($user_id) {
                $query->whereHas('client.user', function ($q) use ($user_id) {
                    $q->where('id', $user_id);
                });
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($referral) {
                return [
                    'id' => $referral->id,
                    'client' => $referral->client?->fullname() ?? '',
                    'referral_to' => $referral->referral_to,
                    'remarks' => $referral->remarks,
                ];
            });
    }

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
