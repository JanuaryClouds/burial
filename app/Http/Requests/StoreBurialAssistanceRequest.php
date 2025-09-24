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

    protected function prepareForValidation(): void {
        $this->merge([
            'status' => 'pending', // Default status
        ]);
    }
    public function rules(): array
    {
        return [
            'deceased.first_name' => 'required|string|max:255',
            'deceased.middle_name' => 'nullable|string|max:255',
            'deceased.last_name' => 'required|string|max:255',
            'deceased.suffix'=> 'nullable|string|max:64',
            'deceased.gender' => 'required|numeric|exists:sexes,id',
            'deceased.religion_id' => 'required|numeric|exists:religions,id',
            'deceased.date_of_birth' => 'required|date',
            'deceased.date_of_death' => 'required|date',
            
            'claimant.first_name' => 'required|string|max:255',
            'claimant.middle_name' => 'nullable|string|max:255',
            'claimant.last_name' => 'required|string|max:255',
            'claimant.suffix'=> 'nullable|string|max:64',
            'claimant.relationship_to_deceased' => 'required|numeric|exists:relationships,id',
            'claimant.mobile_number' => 'required|string|digits:11',
            'claimant.address' => 'required|string|max:255',
            'claimant.barangay_id' => 'required|exists:barangays,id',

            'burial_assistance.funeraria' => 'required|string|max:255',
            'burial_assistance.amount' => 'nullable|string|max:255',
            'burial_assistance.remarks'   => 'nullable|string|max:255',
            'swa'    => 'nullable|string|max:255',
            'encoder'=> 'nullable|string|max:255',
            'initial_checker' => 'nullable|exists:users,id',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

            // System-generated
            // 'tracking_no' => handled in model, not request
            // 'application_date' => handled in controller, not request
            'status' => 'in:pending,approved,rejected'
        ];
    }
}
