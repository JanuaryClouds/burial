<?php

namespace App\Services;

use App\Models\Client;
use App\Models\ClientDemographic;
use App\Models\ClientSocialInfo;

class ClientService
{
    public function storeClient(array $data): ?Client
    {
        $client = Client::create([
            'tracking_no' => $data['tracking_no'],
            'first_name' => $data['first_name'],
            'middle_name' => $data['middle_name'],
            'last_name' => $data['last_name'],
            'age' => $data['age'],
            'date_of_birth' => $data['date_of_birth'],
            'house_no' => $data['house_no'],
            'street' => $data['street'],
            'district_id' => $data['district_id'],
            'barangay_id' => $data['barangay_id'],
            'city' => $data['city'],
            'contact_no' => $data['contact_no'],
        ]);
    
        if ($client) {
            $demographic = ClientDemographic::create([
                'client_id' => $client->id,
                'sex_id' => $data['sex_id'],
                'religion_id' => $data['religion_id'],
                'nationality_id' => $data['nationality_id'],
            ]);
    
            $social = ClientSocialInfo::create([
                'client_id' => $client->id,
                'relationship_id' => $data['relationship_id'],
                'civil_id' => $data['civil_id'],
                'education_id' => $data['education_id'],
                'income' => $data['income'],
                'philhealth' => $data['philhealth'],
                'skill' => $data['skill'],
            ]);
    
            if ($demographic && $social) {
                return $client;
            } else {
                $client->delete();
            }
        }
        return null;
    }
    

    public function updateClient(array $data, $client): Client
    {
        return $client->update($data) ? $client : null;
    }

    public function deleteClient($client): Client
    {
        return $client->delete() ? $client : null;
    }
}