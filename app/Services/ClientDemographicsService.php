<?php

namespace App\Services;

use App\Models\Client;
use App\Models\ClientDemographic;
use App\Services\Auth\AccountAccess;

class ClientDemographicsService
{
    public function __construct()
    {
        //
    }

    public function index()
    {
        //
    }

    /**
     * Summary of store
     */
    public function store(array $data, Client $client): ClientDemographic
    {
        app(AccountAccess::class)->assertWritable();

        return ClientDemographic::create([
            'client_uuid' => $client->uuid,
            'sex_id' => $data['sexId'],
            'nationality_id' => $data['nationalityId'],
            'religion_id' => $data['religionId'],
        ]);
    }

    public function edit()
    {
        //
    }

    /**
     * Summary of update
     *
     * @return ClientDemographic|null
     */
    public function update(ClientDemographic $demographic, array $data): ClientDemographic
    {
        app(AccountAccess::class)->assertWritable();
        $demographic->update([
            'sex_id' => $data['sexId'],
            'nationality_id' => $data['nationalityId'],
            'religion_id' => $data['religionId'],
        ]);

        return $demographic->fresh();
    }

    public function destroy()
    {
        //
    }
}
