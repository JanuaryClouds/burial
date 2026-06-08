<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReligionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:religions,name,'.$this->route('religion')?->id,
            'remarks' => 'nullable|string|max:255',
        ];
    }
}
