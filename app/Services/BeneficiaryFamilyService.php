<?php

namespace App\Services;

use App\Models\BeneficiaryFamily;

class BeneficiaryFamilyService
{
    /**
     * Update beneficiary family data.
     * @param array $data
     * @param int $id
     * @return void
     */
    public function update(array $data, $id): void
    {
        $family = BeneficiaryFamily::findOrFail($id);
        $data =  [
            'fam_name' => $data['fam_name'] ?? null,
            'fam_sex_id' => $data['fam_sex_id'] ?? null,
            'fam_age' => $data['fam_age'] ?? null,
            'fam_civil_id' => $data['fam_civil_id'] ?? null,
            'fam_relationship_id' => $data['fam_relationship_id'] ?? null,
            'fam_occupation' => $data['fam_occupation'] ?? null,
            'fam_income' => $data['fam_income'] ?? null,
        ];
        $family->update($data);
    }
}