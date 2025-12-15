<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProcessLogRequest extends FormRequest
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
            'date_in' => 'required|date',
            'date_out' => 'nullable|date|before_or_equal:date_in',
            'comments' => 'nullable|string',
            'extra_data' => 'sometimes|array',
            'added_by' => 'nullable|exists:users,id',
        ];
    }
}
