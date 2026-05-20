<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBeneficiaryFamilyRequest extends FormRequest
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
            'fam_name' => $this->clean($this->fam_name),
            'fam_income' => $this->clean($this->fam_income),
            'fam_occupation' => $this->clean($this->fam_occupation),
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
            'fam_name' => 'required|max:255',
            'fam_sex_id' => 'required|exists:sexes,id',
            'fam_age' => 'required|numeric',
            'fam_civil_id' => 'required|exists:civil_statuses,id',
            'fam_relationship_id' => 'required|exists:relationships,id',
            'fam_occupation' => 'nullable',
            'fam_income' => 'nullable',
        ];
    }
}
