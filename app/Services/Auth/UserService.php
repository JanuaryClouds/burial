<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserService
{
    public function login(array $data)
    {
        if(Auth::attempt($data))
        {
            $user = Auth::user();
            Auth::login($user);
            return $user;
        }

        return redirect()->back()->with("error","Invalid login credentials.");
    }

    public function logout(): void
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerate();
    }
}