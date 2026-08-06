<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreApplicationRequest extends FormRequest
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
            'client_uuid' => $this->input('client_uuid') ?? session('client_uuid'),
            'beneficiary_uuid' => $this->input('beneficiary_uuid') ?? session('beneficiary_uuid'),
            'relationship_id' => $this->clean($this->input('relationship_id')),
        ]);
    }

    private function clean(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return trim(preg_replace('/\s+/u', ' ', $value));
    }

    public function rules(): array
    {
        return [
            'client_uuid' => 'required|string|exists:clients,uuid',
            'beneficiary_uuid' => 'required|string|exists:beneficiaries,uuid',
            'relationship_id' => 'required|numeric|exists:relationships,id',
            'images.*' => 'nullable|image|max:10240',
        ];
    }

    public function messages(): array
    {
        return [
            'client_uuid.required' => 'Please select a client record.',
            'client_uuid.exists' => 'The selected client record does not exist.',
            'beneficiary_uuid.required' => 'Please select a beneficiary record.',
            'beneficiary_uuid.exists' => 'The selected beneficiary record does not exist.',
            'relationship_id.required' => 'Please select your relationship to the beneficiary.',
            'images.*.image' => 'Uploaded documents must be image files (JPEG or PNG).',
            'images.*.max' => 'Uploaded documents must not exceed 10MB.',
        ];
    }
}
