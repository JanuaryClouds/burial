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
        if (auth()->id() !== $this->route('user')?->id) {
            return;
        }
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
        $owner = auth()->id() === $this->route('user')?->id;

        return [
            'mfa_code' => ['sometimes', 'nullable', 'string', 'max:128'],
            'email' => ['prohibited'],
            'first_name' => $owner ? 'required|string|max:255' : 'prohibited',
            'middle_name' => $owner ? 'nullable|string|max:255' : 'prohibited',
            'last_name' => $owner ? 'required|string|max:255' : 'prohibited',
            'suffix' => $owner ? 'nullable|string|max:64' : 'prohibited',
            'contact_number' => $owner ? 'required|string|max:32' : 'prohibited',
            'is_active' => 'nullable|boolean',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
            'current_password' => ['prohibited'],
            'password' => ['prohibited'],
            'password_confirmation' => ['prohibited'],
        ];
    }
}
