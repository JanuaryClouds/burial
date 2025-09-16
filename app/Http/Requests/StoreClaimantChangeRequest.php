<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClaimantChangeRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            "claimant.first_name" => "required|string|max:255",
            "claimant.middle_name" => "nullable|string|max:255",
            "claimant.last_name" => "required|string|max:255",
            'claimant.suffix'=> 'nullable|string|max:64',
            'claimant.relationship_to_deceased' => 'required|numeric|exists:relationships,id',
            'claimant.mobile_number' => 'required|string|digits:11',
            'claimant.address' => 'required|string|max:255',
            'claimant.barangay_id' => 'required|exists:barangays,id',
            'reason_for_change' => 'required|string|max:255',
        ];
    }
}
