<?php

namespace App\Services;

use App\Models\Beneficiary;
use App\Models\BeneficiaryFamily;

class BeneficiaryFamilyService
{
    public function store(array $data, Beneficiary $beneficiary)
    {
        if (is_array($data['fam_name']) && count($data['fam_name']) > 0) {
            foreach ($data['fam_name'] as $index => $name) {
                BeneficiaryFamily::create([
                    'beneficiary_uuid' => $beneficiary->uuid,
                    'name' => $name,
                    'sex_id' => $data['fam_sex_id'][$index],
                    'age' => $data['fam_age'][$index],
                    'civil_id' => $data['fam_civil_id'][$index],
                    'relationship_id' => $data['fam_relationship_id'][$index],
                    'occupation' => $data['fam_occupation'][$index],
                    'income' => $data['fam_income'][$index],
                ]);
            }
        }
    }

    /**
     * Summary of update
     * @param array $data
     * @param BeneficiaryFamily $member
     * @return void
     */
    public function update(array $data, BeneficiaryFamily $member): void
    {
        $data = [
            'name' => $data['fam_name'] ?? null,
            'sex_id' => $data['fam_sex_id'] ?? null,
            'age' => $data['fam_age'] ?? null,
            'civil_id' => $data['fam_civil_id'] ?? null,
            'relationship_id' => $data['fam_relationship_id'] ?? null,
            'occupation' => $data['fam_occupation'] ?? null,
            'income' => $data['fam_income'] ?? null,
        ];
        $member->update($data);
    }
}
