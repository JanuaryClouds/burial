<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BurialAssistanceFormRequest extends FormRequest
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
            "tracking_no" => "required|string|max:255|unique:burial_assistances,tracking_no",
            "application_date" => "required|date",
            "swa" => "string|nullable|max:255",
            "encoder" => "nullable|exists:users,id",
            "funeraria" => "string|nullable|max:255",
            "deceased_id" => "required|numeric|exists:deceased,id",
            "claimant_id" => "required|numeric|exists:claimants,id",
            "amount" => "numeric|nullable",
            // "status" => "string|nullable|max:255",
            "remarks" => "string|nullable|max:255",
            "initial_checker" => "nullable|exists:users,id",
        ];
    }
}
