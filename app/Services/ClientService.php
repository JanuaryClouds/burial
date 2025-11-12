<?php

namespace App\Services;

use App\Models\Client;
use App\Models\ClientBeneficiary;
use App\Models\ClientDemographic;
use App\Models\ClientRecommendation;
use App\Models\ClientSocialInfo;
use App\Models\ClientAssessment;
use App\Models\ClientBeneficiaryFamily;
use App\Models\Claimant;
use App\Models\Deceased;
use App\Models\BurialAssistance;
use App\Models\FuneralAssistance;
use Str;

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
        
        return $year . '-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    public function storeClient(array $data): ?Client
    {
        $tracking_no = $this->generateTrackingNo();
        
        $client = Client::create([
            'id' => Str::uuid(),
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
                'id' => Str::uuid(),
                'client_id' => $client->id,
                'sex_id' => $data['sex_id'],
                'religion_id' => $data['religion_id'],
                'nationality_id' => $data['nationality_id'],
            ]);
    
            $social = ClientSocialInfo::create([
                'id' => Str::uuid(),
                'client_id' => $client->id,
                'relationship_id' => $data['relationship_id'],
                'civil_id' => $data['civil_id'],
                'education_id' => $data['education_id'],
                'income' => $data['income'],
                'philhealth' => $data['philhealth'],
                'skill' => $data['skill'],
            ]);

            $beneficiary = ClientBeneficiary::create([
                'id' => Str::uuid(),
                'client_id' => $client->id,
                'first_name' => $data['ben_first_name'],
                'middle_name' => $data['ben_middle_name'],
                'last_name' => $data['ben_last_name'],
                'suffix' => $data['ben_suffix'] ?? '',
                'religion_id' => $data['ben_religion_id'],
                'barangay_id' => $data['ben_barangay_id'],
                'sex_id' => $data['ben_sex_id'],
                'date_of_birth' => $data['ben_date_of_birth'],
                'date_of_death' => $data['ben_date_of_death'],
                'place_of_birth' => $data['ben_place_of_birth'],
            ]);

            foreach ($data['fam_name'] as $index => $name) 
            {
                ClientBeneficiaryFamily::create([
                    'id' => Str::uuid(),
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
            

            // $assessmentRows = [];
            // foreach($data['ass_problem_presented'] as $index => $problem) 
            // {
            //     $assessmentRows[] = ClientAssessment::create([
                    // 'id' => Str::uuid(),
            //         'client_id' => $client->id,
            //         'problem_presented' => $problem,
            //         'assessment' => $data['ass_assessment'][$index],
            //     ]);
            // }

            // $assistanceIds = $data['rec_assistance_id'] ?? [];
            // foreach ($assistanceIds as $assistanceId) {
            //     if ($assistanceId == 8) { 
            //         ClientRecommendation::create([
                        // 'id' => Str::uuid(),
            //             'client_id'     => $client->id,
            //             'assistance_id' => 8,
            //             'referral'      => $data['rec_burial_referral'][0] ?? null,
            //             'moa_id'        => $data['rec_moa'][0] ?? null,
            //             'amount'        => $data['rec_amount'][0] ?? null,
            //             'others'        => null,
            //         ]);
            //     } elseif ($assistanceId == 14) { 
            //         ClientRecommendation::create([
                            // 'id' => Str::uuid(),
            //             'client_id'     => $client->id,
            //             'assistance_id' => 14,
            //             'referral'      => null,
            //             'moa_id'        => null,
            //             'amount'        => null,
            //             'others'        => $data['rec_assistance_other'][0] ?? null,
            //         ]);
            //     } else {
            //         ClientRecommendation::create([
                            // 'id' => Str::uuid(),
            //             'client_id'     => $client->id,
            //             'assistance_id' => $assistanceId,
            //             'referral'      => null,
            //             'moa_id'        => null,
            //             'amount'        => null,
            //             'others'        => null,
            //         ]);
            //     }
            // }
    
            if ($demographic && $social && $beneficiary) {
                return $client;
            } else {
                $client->delete();
            }
        }
        return null;
    }
    
    public function transferClient($client_id)
    {
        $client = Client::find($client_id);
        if ($client && $client->beneficiary && $client->assessment->count() > 0 && $client->recommendation->count() > 0 && $client->interviews->count() > 0) {
            if ($client->recommendation->first()->type == 'burial') {
                $claimant = Claimant::create([
                    'id' => Str::uuid(),
                    'first_name' => $client->first_name,
                    'middle_name' => $client->middle_name ?? null,
                    'last_name' => $client->last_name,
                    'suffix' => $client->suffix ?? null,
                    'relationship_to_deceased' => $client->socialInfo->relationship->id,
                    'mobile_number' => $client->contact_no,
                    'address' => $client->house_no . ' ' . $client->street,
                    'barangay_id' => $client->barangay_id
                ]);

                $deceased = Deceased::create([
                    'id' => Str::uuid(),
                    'first_name' => $client->beneficiary->first_name,
                    'middle_name' => $client->beneficiary->middle_name ?? null,
                    'last_name' => $client->beneficiary->last_name,
                    'suffix' => $client->beneficiary->suffix ?? null,
                    'date_of_birth' => $client->beneficiary->date_of_birth,
                    'date_of_death' => $client->beneficiary->date_of_death,
                    'gender' => $client->beneficiary->sex_id,
                    'address' => $client->beneficiary->place_of_birth,
                    'religion_id' => $client->beneficiary->religion_id,
                    'barangay_id' => $client->beneficiary->barangay_id
                ]);

                $burialAssistance = BurialAssistance::create([
                    'id' => Str::uuid(),
                    'tracking_code' => strtoupper(Str::random(6)),
                    'application_date' => $client->created_at,
                    'swa' => $client->assessment->first()->assessment,
                    'encoder' => auth()->user()->id,
                    'claimant_id' => $claimant->id,
                    'deceased_id' => $deceased->id,
                    'funeraria' => $client->recommendation->first()->referral,
                    'amount' => $client->recommendation->first()->amount,
                    'remarks' => $client->recommendation->first()->remarks,
                ]);

                return $burialAssistance;
            } elseif ($client->recommendation->first()->type == 'funeral') {
                $funeralAssistance = FuneralAssistance::create([
                    'id' => Str::uuid(),
                    'client_id' => $client->id,
                    'remarks' => $client->recommendation->first()->remarks
                ]);
                    
                return $funeralAssistance;
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