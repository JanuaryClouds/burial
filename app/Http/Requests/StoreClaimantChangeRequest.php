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
            'reason_for_change' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|exists:users,email',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'suffix' => 'nullable|string|max:64',
            'date_of_birth' => 'required|date',
            'address' => 'required|string|max:255',
            'barangay_id' => 'required|numeric|exists:barangays,id',
            'city' => 'required|string|max:50',
            'contact_number' => 'required|string|max:11',
            'relationship_id' => 'required|numeric|exists:relationships,id',
        ];
    }
}
