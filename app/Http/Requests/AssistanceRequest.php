<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssistanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:assistances,name'.$this->route('assistance')?->id,
            'remarks' => 'nullable|string|max:255',
        ];
    }
}
