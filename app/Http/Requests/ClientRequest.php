<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    
    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'suffix' => 'nullable|string|max:64',
            'age' => 'required|numeric|min:0',
            'date_of_birth' => 'required|date',
            'house_no' => 'required|string|max:20',
            'street' => 'required|string|max:255',
            'district_id' => 'required|numeric|exists:districts,id',
            'barangay_id' => 'required|numeric|exists:barangays,id',
            'city' => 'required|string|max:50',
            'contact_no' => 'required|string|max:11',
            'sex_id' => 'required|numeric|exists:sexes,id',
            'religion_id' => 'required|numeric|exists:religions,id',
            'nationality_id' => 'required|numeric|exists:nationalities,id',
            'relationship_id' => 'required|numeric|exists:relationships,id',
            'education_id' => 'nullable|numeric|exists:educations,id',
            'civil_id' => 'required|numeric|exists:civil_statuses,id',
            'income' => 'nullable|string|max:255',
            'philhealth' => 'nullable|string|max:255',
            'skill' => 'nullable|string|max:255',
            'ben_first_name' => 'required|string|max:255',
            'ben_middle_name' => 'nullable|string|max:255',
            'ben_last_name' => 'required|string|max:255',
            'ben_suffix' => 'nullable|string|max:64',
            'ben_sex_id' => 'required|numeric|exists:sexes,id',
            'ben_religion_id' => 'required|numeric|exists:religions,id',
            'ben_date_of_birth' => 'required|date',
            'ben_date_of_death' => 'required|date',
            'ben_place_of_birth' => 'required|string|max:255',
            'ben_barangay_id' => 'required|numeric|exists:barangays,id',
            'fam_name.*' => 'nullable|string|max:255',
            'fam_sex_id.*' => 'nullable|numeric|exists:sexes,id',
            'fam_age.*' => 'nullable|numeric|min:0',
            'fam_civil_id.*' => 'nullable|numeric|exists:civil_statuses,id',
            'fam_relationship_id.*' => 'nullable|numeric|exists:relationships,id',
            'fam_occupation.*' => 'nullable|string|max:255',
            'fam_income.*' => 'nullable|string|max:255',
            'ass_problem_presented' => 'nullable|string|max:1000',
            'ass_assessment' => 'nullable|string|max:1000',
            'rec_assistance_id'  => 'nullable|exists:assistances,id',
            'rec_burial_referral' => 'nullable|string|max:255',
            'rec_moa' => 'nullable|numeric|exists:mode_of_assistances,id',
            'rec_amount' => 'nullable|string|max:255',
            'rec_assistance_other' => 'nullable|string|max:255',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }
}