<?php

namespace App\Http\Requests;

use App\Http\Requests\Traits\NormalizesInput;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBeneficiaryRequest extends FormRequest
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
            'sex_id' => $this->clean($this->sex_id),
            'date_of_birth' => $this->clean($this->date_of_birth),
            'date_of_death' => $this->clean($this->date_of_death),
            'house_no' => $this->clean($this->house_no),
            'street' => $this->clean($this->street),
            'city' => $this->clean($this->city),
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
            'suffix' => 'nullable|string|max:255',
            'sex_id' => 'required|exists:sexes,id',
            'religion_id' => 'required|exists:religions,id',
            'date_of_birth' => 'required|date',
            'date_of_death' => 'required|date|after_or_equal:date_of_birth',
            'house_no' => 'required|string|max:255',
            'street' => 'nullable|string|max:255',
            'barangay_id' => 'required|exists:barangays,id',
            'district_id' => 'required|exists:districts,id',
            'city' => 'required|string|max:255',
        ];
    }
}
