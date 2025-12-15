<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:sexes,name,'.$this->route('sex')?->id,
            'remarks' => 'nullable|string|max:255',
        ];
    }
}
