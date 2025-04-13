<?php

namespace App\Services;

use App\Models\Client;
use App\Models\ClientBeneficiary;
use App\Models\ClientDemographic;
use App\Models\ClientSocialInfo;
use App\Models\ClientBeneficiaryFamily;

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

            $beneficiary = ClientBeneficiary::create([
                'client_id' => $client->id,
                'first_name' => $data['ben_first_name'],
                'middle_name' => $data['ben_middle_name'],
                'last_name' => $data['ben_last_name'],
                'sex_id' => $data['ben_sex_id'],
                'date_of_birth' => $data['ben_date_of_birth'],
                'place_of_birth' => $data['ben_place_of_birth'],
            ]);

            $familyRows = [];
            foreach ($data['fam_name'] as $index => $name) {
                $familyRows[] = ClientBeneficiaryFamily::create([
                    'client_id' => $client->id,
                    'name' => $name,
                    'sex_id' => $data['fam_sex_id'][$index],
                    'age' => $data['fam_age'][$index],
                    'civil_id' => $data['fam_civil_id'][$index],
                    'relationship_id' => $data['fam_relationship_id'][$index],
                    'occupation' => $data['fam_occupation'][$index],
                    'income' => $data['fam_income'][$index],
                ]);
            }
    
            if ($demographic && $social && $beneficiary && count($familyRows)) {
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