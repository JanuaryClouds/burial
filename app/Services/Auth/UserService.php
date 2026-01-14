<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class UserService
{
    public function login(array $data)
    {
        if (Auth::attempt($data)) {
            $user = Auth::user();
            Auth::login($user);

            return $user;
        }

        return redirect()->back()->with('error', 'Invalid login credentials.');
    }

    public function logout(): void
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerate();
    }

    public function storeUser(array $data)
    {
        $user = User::create($data);
        $user->assignRole('admin');

        return $user;
    }

    public function update(array $data, $user)
    {
        $user->update($data);
        if (isset($data['is_active'])) {
            $user->is_active = 1;
            $user->save();
        } else {
            $user->is_active = 0;
            $user->save();
        }
        $role = Role::firstOrCreate(['name' => $data['role']]);
        $user->syncRoles($role);

        return $user;
    }
}
