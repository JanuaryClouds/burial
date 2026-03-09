<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBurialAssistanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'status' => 'pending', // Default status
        ]);
    }

    public function rules(): array
    {
        return [
            'beneficiary.first_name' => 'required|string|max:255',
            'beneficiary.middle_name' => 'nullable|string|max:255',
            'beneficiary.last_name' => 'required|string|max:255',
            'beneficiary.suffix' => 'nullable|string|max:64',
            'beneficiary.sex_id' => 'required|numeric|exists:sexes,id',
            'beneficiary.religion_id' => 'required|numeric|exists:religions,id',
            'beneficiary.date_of_birth' => 'required|date|before_or_equal:beneficiary.date_of_death',
            'beneficiary.date_of_death' => 'required|date|after_or_equal:beneficiary.date_of_birth|before_or_equal:today',
            'beneficiary.place_of_birth' => 'required|string|max:255',
            'beneficiary.barangay_id' => 'required|numeric|exists:barangays,id',

            'claimant.first_name' => 'required|string|max:255',
            'claimant.middle_name' => 'nullable|string|max:255',
            'claimant.last_name' => 'required|string|max:255',
            'claimant.suffix' => 'nullable|string|max:64',
            'claimant.relationship_to_deceased' => 'required|numeric|exists:relationships,id',
            'claimant.mobile_number' => 'required|string|digits:11',
            'claimant.address' => 'required|string|max:255',
            'claimant.barangay_id' => 'required|numeric|exists:barangays,id',

            'funeraria' => 'required|string|max:255',
            'amount' => 'nullable|decimal:0,2|min:0',
            'remarks' => 'nullable|string|max:255',
            'swa' => 'nullable|string|max:255',
            'encoder' => 'nullable|exists:users,id',
            'initial_checker' => 'nullable|exists:users,id',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

            'status' => 'in:pending,approved,rejected',
        ];
    }
}
