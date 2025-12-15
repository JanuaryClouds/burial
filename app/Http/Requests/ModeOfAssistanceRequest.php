<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ModeOfAssistanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:mode_of_assistances,name,'.$this->route('moa')?->id,
            'remarks' => 'nullable|string|max:255',
        ];
    }
}
