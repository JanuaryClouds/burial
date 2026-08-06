<?php

namespace App\Http\Requests;

use App\Http\Requests\Traits\NormalizesInput;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBeneficiaryRequest extends FormRequest
{
    use NormalizesInput;

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
            'fam_name' => $this->cleanArray($this->fam_name),
            'fam_age' => $this->cleanArray($this->fam_age),
            'fam_income' => $this->cleanArray($this->fam_income),
            'fam_occupation' => $this->cleanArray($this->fam_occupation),
        ]);
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
            'house_no' => 'required|string|max:20',
            'street' => 'required|string|max:255',
            'barangay_id' => 'required|numeric|exists:barangays,id',
            'district_id' => 'required|numeric|exists:districts,id',
            'city' => 'required|string|max:50',
            'sex_id' => 'required|numeric|exists:sexes,id',
            'religion_id' => 'required|numeric|exists:religions,id',
            'date_of_birth' => 'required|date|before:today',
            'date_of_death' => 'required|date|after_or_equal:date_of_birth',
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
        ];
    }
}
