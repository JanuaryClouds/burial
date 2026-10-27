<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
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
        abort_unless(auth()->user()?->isSystemAdministrator(), 403);
        $roles = $this->selectedRoles($data['roles'] ?? []);

        return DB::transaction(function () use ($data, $roles) {
            $user = User::create([
                'first_name' => $data['firstName'], 'middle_name' => $data['middleName'] ?? null,
                'last_name' => $data['lastName'], 'suffix' => $data['suffix'] ?? null,
                'email' => $data['email'], 'contact_number' => $data['contactNumber'],
                'is_active' => true, 'password' => $data['password'],
            ]);
            $user->syncRoles(array_merge(['staff'], $roles));

            return $user;
        });
    }

    public function update(array $data, User $user)
    {
        Gate::authorize('update', $user);
        $admin = auth()->user()->isSystemAdministrator();
        $selected = $this->selectedRoles($data['roles'] ?? []);
        $current = $user->roles()->whereNotIn('name', ['staff', 'superadmin'])->pluck('name')->all();
        if (! $admin) {
            sort($selected);
            sort($current);
            abort_unless($selected === $current, 403);
        }

        return DB::transaction(function () use ($data, $user, $admin, $selected) {
            $attributes = [
                'first_name' => $data['firstName'], 'middle_name' => $data['middleName'] ?? null,
                'last_name' => $data['lastName'], 'suffix' => $data['suffix'] ?? null,
                'email' => $data['email'], 'contact_number' => $data['contactNumber'],
            ];
            if (filled($data['newPassword'] ?? null)) {
                $attributes['password'] = $data['newPassword'];
            }
            if ($admin) {
                $attributes['is_active'] = $data['isActive'];
                $protected = $user->roles()->whereIn('name', ['staff', 'superadmin'])->pluck('name')->all();
                if ($user->isInternal() || $selected) {
                    $protected[] = 'staff';
                }
                $user->syncRoles(array_unique(array_merge($protected, $selected)));
            }
            $user->update($attributes);

            return $user;
        });
    }

    private function selectedRoles(array $input): array
    {
        $available = Role::where('guard_name', 'web')->whereNotIn('name', ['staff', 'superadmin'])->pluck('name')->all();
        $selected = [];
        foreach ($input as $name => $value) {
            if (! in_array($name, $available, true) || ! in_array($value, [true, false, null, 'true', 'false', $name], true)) {
                throw ValidationException::withMessages(['roles' => 'Choose valid operational roles.']);
            }
            if (in_array($value, [true, 'true', $name], true)) {
                $selected[] = $name;
            }
        }

        return $selected;
    }
}
