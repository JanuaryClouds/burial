<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateClientRequest extends FormRequest
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
            'house_no' => $this->clean($this->house_no),
            'street' => $this->clean($this->street),
            'city' => $this->clean($this->city),
            'contact_number' => $this->normalizePhone($this->contact_number),
            'income' => $this->clean($this->income),
            'philhealth' => $this->clean($this->philhealth),
            'skill' => $this->clean($this->skill),
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

    private function cleanArray(mixed $value): ?array
    {
        if ($value === null || ! is_array($value)) {
            return null;
        }

        $clean = function ($value) use (&$clean) {
            if (is_array($value)) {
                return array_map($clean, $value);
            }

            if (is_string($value)) {
                $value = trim($value);

                return preg_replace('/\s+/u', ' ', $value);
            }

            return $value;
        };

        return array_map($clean, $value);
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
            'suffix' => 'nullable|string|max:255',
            'age' => 'required|min:1|max:120',
            'sex_id' => 'required|exists:sexes,id',
            'date_of_birth' => 'required|date',
            'house_no' => 'required|string|max:255',
            'street' => 'nullable|string|max:255',
            'barangay_id' => 'required|exists:barangays,id',
            'district_id' => 'required|exists:districts,id',
            'city' => 'required|string|max:255',
            'relationship_id' => 'required|exists:relationships,id',
            'civil_id' => 'required|exists:civil_statuses,id',
            'religion_id' => 'required|exists:religions,id',
            'nationality_id' => 'required|exists:nationalities,id',
            'education_id' => 'nullable|exists:educations,id',
            'income' => 'nullable|string|max:255',
            'philhealth' => 'nullable|string|max:255',
            'skill' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:11',
        ];
    }
}
