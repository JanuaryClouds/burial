<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TrimStrings as Middleware;

class TrimStrings extends Middleware
{
    /**
     * The names of the attributes that should not be trimmed.
     *
     * @var array<int, string>
     */
    protected $except = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    protected function transform($key, $value)
    {
        // Livewire nests update properties and action parameters in its JSON body.
        if (request()->header('X-Livewire') !== null || preg_match('/(?:^|\.)(?:currentPassword|newPassword|confirmNewPassword|confirmPassword)$/', $key)) {
            return $value;
        }

        return parent::transform($key, $value);
    }
}
