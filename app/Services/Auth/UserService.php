<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserService
{
    public function login(array $data)
    {
        if (Auth::attempt([...$data, 'citizen_uuid' => null, 'is_active' => true])) {
            $user = Auth::user();
            if (! app(AccountAccess::class)->isInternal($user)) {
                Auth::logout();
                throw new \RuntimeException('Invalid login credentials.');
            }
            session(['account.staff_hash' => $user->getAuthPassword()]);

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
        app(AccountAccess::class)->assertWritable();
        abort_unless(auth()->user()?->isSystemAdministrator(), 403);
        abort_if(filled($data['citizen_uuid'] ?? null), 403);
        Validator::make($data, [
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password' => PasswordPolicy::rules(),
            'confirmPassword' => ['required', 'same:password'],
        ])->validate();
        $roles = $this->selectedRoles($data['roles'] ?? []);

        return DB::transaction(function () use ($data, $roles) {
            $user = User::create([
                'first_name' => $data['firstName'], 'middle_name' => $data['middleName'] ?? null,
                'last_name' => $data['lastName'], 'suffix' => $data['suffix'] ?? null,
                'email' => $data['email'], 'contact_number' => $data['contactNumber'],
                'is_active' => true, 'password' => $data['password'], 'password_change_required' => true,
            ]);
            $user->syncRoles(array_merge(['staff'], $roles));

            return $user;
        });
    }

    public function update(array $data, User $user)
    {
        app(AccountAccess::class)->assertWritable();
        foreach (['password', 'newPassword', 'new_password', 'currentPassword', 'current_password', 'confirmNewPassword', 'password_confirmation', 'citizen_uuid', 'password_change_required'] as $field) {
            abort_if(filled($data[$field] ?? null), 403, 'Use your own password-change form.');
        }
        Gate::authorize('update', $user);
        $admin = auth()->user()->isSystemAdministrator();
        $selected = $this->selectedRoles($data['roles'] ?? []);
        abort_if(filled($user->citizen_uuid) && ($selected || $user->isInternal()), 403, 'Portal accounts cannot be converted into staff accounts.');
        Validator::make($data, [
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'isActive' => ['sometimes', 'boolean'],
        ])->validate();
        $current = $user->roles()->whereNotIn('name', ['staff', 'superadmin'])->pluck('name')->all();
        if (! $admin) {
            sort($selected);
            sort($current);
            abort_unless($selected === $current, 403);
        }

        return DB::transaction(function () use ($data, $user, $admin, $selected) {
            // All administrator status changes use the same ordered lock set.
            $administrators = User::whereHas('roles', fn ($q) => $q->where('name', 'superadmin'))
                ->orderBy('id')->lockForUpdate()->get();
            $user = User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            app(AccountAccess::class)->assertWritable();
            abort_if(filled($user->citizen_uuid) && ($selected || $user->isInternal()), 403);
            $attributes = [
                'first_name' => $data['firstName'], 'middle_name' => $data['middleName'] ?? null,
                'last_name' => $data['lastName'], 'suffix' => $data['suffix'] ?? null,
                'email' => $data['email'], 'contact_number' => $data['contactNumber'],
            ];
            if ($admin) {
                $active = (bool) ($data['isActive'] ?? $user->is_active);
                if (! $active && $user->is_active) {
                    abort_if($user->id === auth()->id(), 403, 'You cannot deactivate your own account.');
                    abort_if($administrators->contains('id', $user->id) && $administrators->where('is_active', true)->count() <= 1, 403, 'The last active superadmin cannot be deactivated.');
                }
                $attributes['is_active'] = $active;
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

    public function changePassword(User $user, array $data): void
    {
        $user = $user->fresh(['roles']);
        $access = app(AccountAccess::class);
        abort_unless($user && auth()->id() === $user->id && $access->isInternal($user) && $access->isActive($user), 403);
        $key = 'account.password.'.$user->id;
        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages(['currentPassword' => 'Too many attempts. Please wait one minute.']);
        }
        RateLimiter::hit($key, 60);
        Validator::make($data, [
            'currentPassword' => ['required', 'string'],
            'newPassword' => PasswordPolicy::rules(),
            'confirmNewPassword' => ['required', 'same:newPassword'],
        ])->validate();
        if (! Hash::check($data['currentPassword'], $user->password)) {
            throw ValidationException::withMessages(['currentPassword' => 'The current password is incorrect.']);
        }
        if (Hash::check($data['newPassword'], $user->password)) {
            throw ValidationException::withMessages(['newPassword' => 'Choose a password different from your current password.']);
        }
        try {
            DB::transaction(function () use ($user, $data, $access) {
                $locked = User::whereKey($user->id)->lockForUpdate()->firstOrFail();
                abort_unless($access->isActive($locked) && $access->isInternal($locked), 403);
                if (! Hash::check($data['currentPassword'], $locked->password)) {
                    throw ValidationException::withMessages(['currentPassword' => 'The current password is incorrect.']);
                }
                $locked->forceFill(['password' => $data['newPassword'], 'password_change_required' => false])->save();
                $locked->tokens()->delete();
                activity('account-management')->causedBy($locked)->performedOn($locked)->log('Owner changed password');
            });
        } catch (ValidationException|HttpException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            activity('account-management')->causedBy($user)->withProperties(['exception_type' => $exception::class])->log('Password change failed');
            throw ValidationException::withMessages(['newPassword' => 'The password could not be changed. Please try again.']);
        }
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
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
