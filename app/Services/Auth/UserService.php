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
        return User::select('id', 'emp_id', 'first_name', 'middle_name', 'last_name', 'email', 'contact_number', 'is_active')
            ->whereDoesntHave('roles', function ($query) {
                $query->where('name', 'superadmin');
            })
            ->get()
            ->map(function ($user) {
                $flagAsStaff = $user->roles->contains('name', 'staff');
                $flagAsPotentialStaff = $user->roles->isEmpty() && $user->emp_id != null;

                return [
                    'id' => $user->id,
                    'name' => $user->fullname(),
                    'email' => $user->email,
                    'contact_number' => $user->contact_number ?? 'N/A',
                    'is_active' => $user->is_active,
                    'staff' => $flagAsStaff,
                    'potential_staff' => $flagAsPotentialStaff,
                    'show_route' => route('user.edit', $user->id),
                ];
            });
    }

    // public function storeUser(array $data)
    // {
    //     $user = User::create($data);
    //     $user->assignRole('staff');

    //     return $user;
    // }

    public function update(array $data, User $user)
    {
        if (! empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            $data['password'] = $user->password;
        }

        $user->update($data);
        if (isset($data['is_active'])) {
            $user->is_active = (bool) $data['is_active'];
        } else {
            $user->is_active = 0;
        }
        $user->save();

        $staffRole = Role::where('name', 'staff')->first();
        if (! isset($data['roles']) || empty($data['roles']) || $data['roles'] == []) {
            $user->roles()->detach();
        } else {
            if (! in_array($staffRole->id, $data['roles'])) {
                $user->roles()->detach();
            } else {
                $user->roles()->sync($data['roles']);
            }
        }

        return $user;
    }
}
