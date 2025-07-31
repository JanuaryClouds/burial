<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BurialServiceRequest extends FormRequest
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
            "deceased_firstname" => "string|required|max:255",
            "deceased_lastname" => "string|required|max:255",
            "representative" => "string|required|max:255",
            "representative_contact" => "string|required|max:255",
            "rep_relationship" => "required|numeric|exists:relationships,id",
            "burial_address" => "string|required|max:255",
            "barangay_id" => "required|exists:barangays,id",
            "start_of_burial" => "date|required",
            "end_of_burial"=> "date|required|after:start_of_burial",
            "burial_service_provider" => "required|numeric|exists:burial_service_providers,id",
            "collected_funds" => "string|required|max:255",
            "remarks"=> "nullable|string|max:255",
        ];
    }
}
