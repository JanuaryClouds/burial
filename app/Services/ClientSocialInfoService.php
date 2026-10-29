<?php

namespace App\Services;

use App\Models\Client;
use App\Models\ClientSocialInfo;
use App\Services\Auth\AccountAccess;

class ClientSocialInfoService
{
    public function __construct()
    {
        //
    }

    public function index()
    {
        //
    }

    public function store(array $data, Client $client)
    {
        app(AccountAccess::class)->assertWritable();

        return ClientSocialInfo::create([
            'client_uuid' => $client->uuid,
            'civil_id' => $data['civilId'],
            'education_id' => $data['educationId'],
            'philhealth' => $data['philhealth'],
            'skill' => $data['skill'],
            'income' => $data['income'],
        ]);
    }

    public function edit(string $uuid): ClientSocialInfo
    {
        return ClientSocialInfo::whereUuid($uuid)->firstOrFail();
    }

    public function update(ClientSocialInfo $clientSocialInfo, array $data): ClientSocialInfo
    {
        app(AccountAccess::class)->assertWritable();
        $clientSocialInfo->update([
            'civil_id' => $data['civilId'],
            'education_id' => $data['educationId'],
            'philhealth' => $data['philhealth'],
            'skill' => $data['skill'],
            'income' => $data['income'],
        ]);

        return $clientSocialInfo->fresh();
    }

    public function destroy()
    {
        //
    }
}
