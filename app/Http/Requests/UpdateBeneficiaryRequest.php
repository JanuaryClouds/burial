<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBeneficiaryRequest extends FormRequest
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
            'ben_first_name' => $this->clean($this->ben_first_name),
            'ben_middle_name' => $this->clean($this->ben_middle_name),
            'ben_last_name' => $this->clean($this->ben_last_name),
            'ben_suffix' => $this->clean($this->ben_suffix),
            'ben_sex_id' => $this->clean($this->ben_sex_id),
            'ben_religion_id' => $this->clean($this->ben_religion_id),
            'ben_date_of_birth' => $this->clean($this->ben_date_of_birth),
            'ben_date_of_death' => $this->clean($this->ben_date_of_death),
            'ben_place_of_birth' => $this->clean($this->ben_place_of_birth),
            'ben_barangay_id' => $this->clean($this->ben_barangay_id),
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'ben_first_name' => 'required|string|max:255',
            'ben_middle_name' => 'nullable|string|max:255',
            'ben_last_name' => 'required|string|max:255',
            'ben_suffix' => 'nullable|string|max:255',
            'ben_sex_id' => 'required|exists:sexes,id',
            'ben_religion_id' => 'required|exists:religions,id',
            'ben_date_of_birth' => 'required|date',
            'ben_date_of_death' => 'required|date|after_or_equal:date_of_birth',
            'ben_place_of_birth' => 'required|string|max:255',
            'ben_barangay_id' => 'required|exists:barangays,id',
        ];
    }
}
