<?php

namespace App\Services\Auth;

use Closure;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class PasswordPolicy
{
    public static function validateSeedPassword(mixed $password): void
    {
        $validator = Validator::make(['password' => $password], ['password' => self::rules()], [], ['password' => 'ADMIN_PASSWORD']);
        if ($validator->fails()) {
            throw new \RuntimeException('Cannot create internal accounts: '.implode(' ', $validator->errors()->all())
                .' Update ADMIN_PASSWORD in your environment and refresh cached configuration before retrying. Existing passwords are not reset.');
        }
    }

    public static function rules(): array
    {
        return ['required', 'string', 'not_regex:/[\x00-\x1F\x7F]/', Password::min(12)->mixedCase()->numbers()->symbols(),
            function (string $attribute, mixed $value, Closure $fail): void {
                if (is_string($value) && strlen($value) > 72) {
                    $fail('The password must not exceed 72 bytes. Spaces are preserved.');
                }
            }];
    }
}
