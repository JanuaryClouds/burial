<?php

namespace App\Services;

use App\Models\Client;
use App\Models\ClientDemographic;
use Illuminate\Database\Eloquent\Model;

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
     * @param array $data
     * @param Client $client
     * @return ClientDemographic
     */
    public function store(array $data, Client $client): ClientDemographic
    {
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
     * @param ClientDemographic $demographic
     * @param array $data
     * @return ClientDemographic|null
     */
    public function update(ClientDemographic $demographic, array $data): ClientDemographic
    {
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