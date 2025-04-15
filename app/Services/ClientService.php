<?php

namespace App\Services;

use App\Models\Client;
use App\Models\ClientBeneficiary;
use App\Models\ClientDemographic;
use App\Models\ClientRecommendation;
use App\Models\ClientSocialInfo;
use App\Models\ClientAssessment;
use App\Models\ClientBeneficiaryFamily;

class ClientService
{
    private function generateTrackingNo(): string
    {
        $year = date('Y');
        
        $latestClient = Client::where('tracking_no', 'LIKE', $year . '-%')
            ->orderBy('created_at', 'desc')
            ->first();
    
        if ($latestClient) {
            $parts = explode('-', $latestClient->tracking_no);
            $lastNumber = (int) end($parts);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $year . '-' . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    }

    public function storeClient(array $data): ?Client
    {
        $tracking_no = $this->generateTrackingNo();
        
        $client = Client::create([
            'tracking_no' => $tracking_no,
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
            foreach ($data['fam_name'] as $index => $name) 
            {
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

            $assessmentRows = [];
            foreach($data['ass_problem_presented'] as $index => $problem) 
            {
                $assessmentRows[] = ClientAssessment::create([
                    'client_id' => $client->id,
                    'problem_presented' => $problem,
                    'assessment' => $data['ass_assessment'][$index],
                ]);
            }

            $assistanceIds = $data['rec_assistance_id'] ?? [];

            // Loop through each selected assistance
            foreach ($assistanceIds as $assistanceId) {
                if ($assistanceId == 8) { // Burial assistance
                    ClientRecommendation::create([
                        'client_id'     => $client->id,
                        'assistance_id' => 8,
                        'referral'      => $data['rec_burial_referral'][0] ?? null,
                        'moa_id'        => $data['rec_moa'][0] ?? null,
                        'amount'        => $data['rec_amount'][0] ?? null,
                        'others'        => null,
                    ]);
                } elseif ($assistanceId == 14) { // Others assistance
                    ClientRecommendation::create([
                        'client_id'     => $client->id,
                        'assistance_id' => 14,
                        'referral'      => null,
                        'moa_id'        => null,
                        'amount'        => null,
                        'others'        => $data['rec_assistance_other'][0] ?? null,
                    ]);
                } else { // For other assistance types that do not require extra information
                    ClientRecommendation::create([
                        'client_id'     => $client->id,
                        'assistance_id' => $assistanceId,
                        'referral'      => null,
                        'moa_id'        => null,
                        'amount'        => null,
                        'others'        => null,
                    ]);
                }
            }
    
            if ($demographic && $social && $beneficiary && count($familyRows) && count($assessmentRows) && count($assistanceIds)) {
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