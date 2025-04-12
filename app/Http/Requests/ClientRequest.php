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
            'age' => 'required|numeric',
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
        ];
    }
}