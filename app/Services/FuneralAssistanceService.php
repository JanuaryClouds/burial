<?php

namespace App\Services;
use App\Models\FuneralAssistance;

class FuneralAssistanceService
{
    /**
     * @param array $data Data passed from request
     * @param FuneralAssistance $funeralAssistance Original application
     * @return FuneralAssistance returns the updated funeral assistance and client
     */
    public function update(array $data, $funeralAssistance)
    {
        $funeralAssistance->update($data);
        $client = $funeralAssistance->client;
        
        $client->demographic->update([
            'sex_id' => $data['sex_id'],
            'religion_id' => $data['religion_id'],
            'nationality_id' => $data['nationality_id'],
        ]);

        $client->socialInfo->update([
            'relationship_id' => $data['relationship_id'],
            'civil_id' => $data['civil_id'],
            'education_id' => $data['education_id'],
            'income' => $data['income'],
            'philhealth' => $data['philhealth'],
            'skill' => $data['skill'],
        ]);

        $client->beneficiary->update([
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

        $families = $client->family()->orderBy('id')->get();

        foreach ($families as $index => $family) {
            $family->update([
                'name'            => $data['fam_name'][$index],
                'sex_id'          => $data['fam_sex_id'][$index],
                'age'             => $data['fam_age'][$index],
                'civil_id'        => $data['fam_civil_id'][$index],
                'relationship_id' => $data['fam_relationship_id'][$index],
                'occupation'      => $data['fam_occupation'][$index],
                'income'          => $data['fam_income'][$index],
            ]);
        }
        return $funeralAssistance;
    }
}