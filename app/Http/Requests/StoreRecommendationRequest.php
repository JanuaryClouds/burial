<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreRecommendationRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'funeral_assistance_types' => 'required|array',
            'funeral_assistance_types.*' => 'required|uuid|exists:funeral_assistance_types,uuid',
            'amount_extended' => 'required|numeric|min:0',
            'mode_of_assistance_id' => 'required|exists:mode_of_assistances,id',
        ];
    }
}
