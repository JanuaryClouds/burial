<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReligionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return false;
    }
    
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'remarks' => 'nullable|string|max:255',
        ];
    }
}