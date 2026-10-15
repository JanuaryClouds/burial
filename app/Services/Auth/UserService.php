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

        throw new \RuntimeException('Invalid login credentials.');
    }

    public function logout(): void
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerate();
    }

    public function index()
    {
        return User::select(['id', 'first_name', 'middle_name', 'last_name', 'email', 'contact_number', 'is_active'])
            ->whereDoesntHave('roles', function ($query) {
                $query->where('name', 'superadmin');
            })
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'middle_name' => $user->middle_name != null ? $user->middle_name : 'N/A',
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'contact_number' => $user->contact_number ?? 'N/A',
                    'is_active' => $user->is_active,
                    'show_route' => route('user.edit', $user->id),
                ];
            });
    }

    public function store(array $data)
    {
        $user = User::create([
            'first_name' => $data['firstName'],
            'middle_name' => $data['middleName'],
            'last_name' => $data['lastName'],
            'suffix' => $data['suffix'],
            'email' => $data['email'],
            'contact_number' => $data['contactNumber'],
            'is_active' => true,
            'password' => bcrypt($data['password'])
        ]);

        $user->assignRole('staff');

        return $user;
    }

    public function update(array $data, User $user)
    {
        $user->update([
            'first_name' => $data['firstName'],
            'middle_name' => $data['middleName'],
            'last_name' => $data['lastName'],
            'suffix' => $data['suffix'],
            'email' => $data['email'],
            'contact_number' => $data['contactNumber'],
            'password' => $data['newPassword'] ?? $user->password,
            'is_active' => $data['isActive'],
        ]);

        $user->save();

        // if (! isset($data['roles']) || empty($data['roles']) || $data['roles'] == []) {
        //     $user->roles()->detach();
        // } else {
        //     if (! in_array($staffRole->id, $data['roles'])) {
        //         $user->roles()->detach();
        //     } else {
        //         $user->roles()->sync($data['roles']);
        //     }
        // }

        foreach ($data['roles'] as $roleName => $value) {
            if ($value == true || $value == 'true') {
                $user->assignRole($roleName);
            } else {
                $user->removeRole($roleName);
            }
        }

        return $user;
    }
}
