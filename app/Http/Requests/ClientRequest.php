<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientRequest extends FormRequest
{
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
            'ben_first_name' => $this->clean($this->ben_first_name),
            'ben_middle_name' => $this->clean($this->ben_middle_name),
            'ben_last_name' => $this->clean($this->ben_last_name),
            'ben_suffix' => $this->clean($this->ben_suffix),
            'fam_name' => $this->cleanArray($this->fam_name),
            'fam_occupation' => $this->cleanArray($this->fam_occupation),
            'fam_income' => $this->cleanArray($this->fam_income),
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

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'suffix' => 'nullable|string|max:64',
            'age' => 'required|numeric|min:0',
            'date_of_birth' => 'required|date',
            'house_no' => 'required|string|max:20',
            'street' => 'required|string|max:255',
            'district_id' => 'required|numeric|exists:districts,id',
            'barangay_id' => 'required|numeric|exists:barangays,id',
            'city' => 'required|string|max:50',
            'contact_number' => 'required|string|max:11',
            'sex_id' => 'required|numeric|exists:sexes,id',
            'religion_id' => 'required|numeric|exists:religions,id',
            'nationality_id' => 'required|numeric|exists:nationalities,id',
            'relationship_id' => 'required|numeric|exists:relationships,id',
            'education_id' => 'nullable|numeric|exists:educations,id',
            'civil_id' => 'required|numeric|exists:civil_statuses,id',
            'income' => 'nullable|string|max:255',
            'philhealth' => 'nullable|string|max:255',
            'skill' => 'nullable|string|max:255',
            'ben_first_name' => 'required|string|max:255',
            'ben_middle_name' => 'nullable|string|max:255',
            'ben_last_name' => 'required|string|max:255',
            'ben_suffix' => 'nullable|string|max:64',
            'ben_sex_id' => 'required|numeric|exists:sexes,id',
            'ben_religion_id' => 'required|numeric|exists:religions,id',
            'ben_date_of_birth' => 'required|date',
            'ben_date_of_death' => 'required|date',
            'ben_place_of_birth' => 'required|string|max:255',
            'ben_barangay_id' => 'required|numeric|exists:barangays,id',
            'fam_name' => 'required|array|min:1',
            'fam_sex_id' => 'required|array|min:1',
            'fam_age' => 'required|array|min:1',
            'fam_civil_id' => 'required|array|min:1',
            'fam_relationship_id' => 'required|array|min:1',
            'fam_name.*' => 'required|string|max:255',
            'fam_sex_id.*' => 'required|numeric|exists:sexes,id',
            'fam_age.*' => 'required|numeric|min:0',
            'fam_civil_id.*' => 'required|numeric|exists:civil_statuses,id',
            'fam_relationship_id.*' => 'required|numeric|exists:relationships,id',
            'fam_occupation.*' => 'nullable|string|max:255',
            'fam_income.*' => 'nullable|string|max:255',
            // 'ass_problem_presented' => 'nullable|string|max:1000',
            // 'ass_assessment' => 'nullable|string|max:1000',
            // 'rec_assistance_id' => 'nullable|exists:assistances,id',
            // 'rec_burial_referral' => 'nullable|string|max:255',
            // 'rec_moa' => 'nullable|numeric|exists:mode_of_assistances,id',
            // 'rec_amount' => 'nullable|string|max:255',
            // 'rec_assistance_other' => 'nullable|string|max:255',
            'images.*' => 'nullable|image|max:10240',
        ];
    }
}
