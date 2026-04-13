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

    public function index()
    {
        return User::select('id', 'first_name', 'middle_name', 'last_name', 'email', 'contact_number', 'is_active')
            ->whereDoesntHave('roles', function ($query) {
                $query->where('name', 'superadmin');
            })
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'middle_name' => $user->middle_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'contact_number' => $user->contact_number,
                    'is_active' => $user->is_active,
                    'show_route' => route('user.edit', $user->id),
                ];
            });
    }

    public function storeUser(array $data)
    {
        $data['password'] = config('app.admin_password');
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
        
        if (!isset($data['roles']) || empty($data['roles']) || $data['roles'] == []) {
            $user->roles()->detach();
        } else {
            $user->roles()->sync($data['roles']);
        }

        return $user;
    }
}
