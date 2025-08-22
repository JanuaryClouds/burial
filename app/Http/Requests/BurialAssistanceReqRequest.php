<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BurialAssistanceReqRequest extends FormRequest
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
            'type_of_assistance' => 8, // Default type of assistance
            'status' => 'pending', // Default status
        ]);
    }

    public function rules(): array
    {
        return [
            "deceased_firstname" => "string|required|max:255",
            "deceased_lastname" => "string|required|max:255",
            "representative" => "string|required|max:255",
            "representative_contact" => "string|required|max:255",
            "rep_relationship" => "required|numeric|exists:relationships,id",
            "burial_address" => "string|required|max:255",
            "barangay_id" => "required|numeric|exists:barangays,id",
            "start_of_burial" => "required|date",
            "end_of_burial" => "required|date|after:start_of_burial",
            "service_id" => "nullable|numeric|exists:burial_services,id",
            "remarks" => "string|nullable|max:255",
        ];
    }
}
