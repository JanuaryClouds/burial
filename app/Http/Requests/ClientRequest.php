<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return false;
    }
    
    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'age' => 'required|numeric',
            'date_of_birth' => 'required|date',
            'house_no' => 'required|string|max:20',
            'street' => 'required|string|max:255',
            'district_id' => 'required|numeric|exists:district,id',
            'barangay_id' => 'required|numeric|exists:barangays,id',
            'city' => 'required|string|max:50',
            'contact_no' => 'required|string|max:11'
        ];
    }
}