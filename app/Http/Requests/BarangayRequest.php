<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BarangayRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:barangays,name',
            'district_id' => 'required|numeric|exists:districts,id',
            'remarks' => 'nullable|string|max:255',
        ];
    }
}
