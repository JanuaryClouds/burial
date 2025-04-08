<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CivilStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:civil_statuses,name,' . $this->route('civil')?->id,
            'remarks' => 'nullable|string|max:255',
        ];
    }
}