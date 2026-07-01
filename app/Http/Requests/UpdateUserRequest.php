<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'first_name' => $this->clean($this->first_name),
            'middle_name' => $this->clean($this->middle_name),
            'last_name' => $this->clean($this->last_name),
            'suffix' => $this->clean($this->suffix),
            'contact_number' => $this->normalizePhone($this->contact_number),
        ]);
    }

    private function clean(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }
        $value = trim($value);
        $value = preg_replace('/\s+/u', ' ', $value);

        return $value;
    }

    private function normalizePhone(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return preg_replace('/\D+/', '', $value);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'suffix' => 'nullable|string|max:64',
            'contact_number' => 'required|string|max:11',
            'is_active' => 'nullable|boolean',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ];
    }
}
