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
            'tracking_no' => 'required|string|max:255|unique:clients,tracking_no',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
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
            'education_id' => 'required|numeric|exists:educations,id',
            'civil_id' => 'required|numeric|exists:civil_statuses,id',
            'income' => 'required|string|max:255',
            'philhealth' => 'required|string|max:255',
            'skill' => 'required|string|max:255',
            'ben_first_name' => 'required|string|max:255',
            'ben_middle_name' => 'nullable|string|max:255',
            'ben_last_name' => 'required|string|max:255',
            'ben_sex_id' => 'required|numeric|exists:sexes,id',
            'ben_date_of_birth' => 'required|date',
            'ben_place_of_birth' => 'required|string|max:255',
            'fam_name.*' => 'required|string|max:255',
            'fam_sex_id.*' => 'required|numeric|exists:sexes,id',
            'fam_age.*' => 'required|numeric|min:0',
            'fam_civil_id.*' => 'required|numeric|exists:civil_statuses,id',
            'fam_relationship_id.*' => 'required|numeric|exists:relationships,id',
            'fam_occupation.*' => 'required|string|max:255',
            'fam_income.*' => 'required|string|max:255',
            'ass_problem_presented.*' => 'required|string|max:1000',
            'ass_assessment.*' => 'required|string|max:1000',
        ];
    }
}