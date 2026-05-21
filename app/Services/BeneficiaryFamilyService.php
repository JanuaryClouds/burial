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
            'name' => $data['fam_name'] ?? null,
            'sex_id' => $data['fam_sex_id'] ?? null,
            'age' => $data['fam_age'] ?? null,
            'civil_id' => $data['fam_civil_id'] ?? null,
            'relationship_id' => $data['fam_relationship_id'] ?? null,
            'occupation' => $data['fam_occupation'] ?? null,
            'income' => $data['fam_income'] ?? null,
        ];
        $family->update($data);
    }
}