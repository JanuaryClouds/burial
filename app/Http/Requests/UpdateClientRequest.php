<?php

namespace App\Http\Requests;

use App\Http\Requests\Traits\NormalizesInput;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateClientRequest extends FormRequest
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
            'contact_number' => $this->normalizePhone($this->contact_number),
            'income' => $this->clean($this->income),
            'philhealth' => $this->clean($this->philhealth),
            'skill' => $this->clean($this->skill),
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
            'date_of_birth' => 'required|date',
            'house_no' => 'required|string|max:255',
            'street' => 'nullable|string|max:255',
            'barangay_id' => 'required|exists:barangays,id',
            'district_id' => 'required|exists:districts,id',
            'city' => 'required|string|max:255',
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
