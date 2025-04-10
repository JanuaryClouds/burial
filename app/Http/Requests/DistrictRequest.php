<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DistrictRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:civil_statuses,name,' . $this->route('district')?->id,
            'remarks' => 'nullable|string|max:255',
        ];
    }
}