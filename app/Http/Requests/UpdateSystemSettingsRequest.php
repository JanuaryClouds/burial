<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSystemSettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->hasRole('superadmin') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [];

        foreach (config('system_setting') as $key => $setting) {
            if ($setting['type'] === 'boolean') {
                $rules[$key] = 'boolean';
            }

            if ($setting['type'] === 'integer') {
                $rules[$key] = 'integer';
            }

            if ($setting['type'] === 'string') {
                $rules[$key] = 'string';
            }
        }

        return $rules;
    }
}
