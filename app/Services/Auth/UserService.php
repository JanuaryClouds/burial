<?php

namespace App\Services\Auth;

use App\Models\SuperadminMfaCredential;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserService
{
    public function login(array $data)
    {
        $result = app(LoginService::class)->attempt($data);
        if (! isset($result['redirect'])) {
            throw new \RuntimeException($result['message']);
        }

        return Auth::user();
    }

    public function logout(): void
    {
        app(AuthenticationAudit::class)->record(Auth::user(), 'Staff signed out', actor: Auth::user());
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

    /** Ordinary staff provisioning must use invitations. Seeders create bootstrap users directly. */
    public function store(array $data)
    {
        abort(403, 'Create an email invitation instead.');
    }

    public function update(array $data, User $user)
    {
        app(AccountAccess::class)->assertWritable();
        Gate::authorize('update', $user);
        foreach (['password', 'newPassword', 'new_password', 'currentPassword', 'current_password', 'confirmNewPassword', 'password_confirmation', 'citizen_uuid', 'password_change_required'] as $field) {
            abort_if(array_key_exists($field, $data), 403, 'Use your own password-change form.');
        }

        return DB::transaction(function () use ($data, $user) {
            // Keep the existing ordered lock set for last-administrator protection.
            $administrators = User::whereHas('roles', fn ($q) => $q->where('name', 'superadmin'))
                ->orderBy('id')->lockForUpdate()->get();
            $locked = User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            $actor = app(AccountAccess::class)->assertWritable();
            Gate::authorize('update', $locked);
            $owner = $actor->id === $locked->id;
            $admin = $actor->isSystemAdministrator();
            abort_if(isset($data['email']) && $data['email'] !== $locked->email, 403, 'Account email cannot be changed.');
            $fields = ['firstName' => 'first_name', 'middleName' => 'middle_name', 'lastName' => 'last_name',
                'suffix' => 'suffix', 'contactNumber' => 'contact_number'];
            $attributes = [];
            foreach ($fields as $input => $column) {
                if (array_key_exists($input, $data)) {
                    abort_if(! $owner && $data[$input] !== $locked->$column, 403, 'Only the owner can edit their profile.');
                    if ($owner) {
                        $attributes[$column] = $data[$input];
                    }
                }
            }
            if ($owner) {
                Validator::make(array_merge($locked->only(array_values($fields)), $attributes), [
                    'first_name' => ['required', 'string', 'max:255'], 'last_name' => ['required', 'string', 'max:255'],
                    'middle_name' => ['nullable', 'string', 'max:255'], 'suffix' => ['nullable', 'string', 'max:64'],
                    'contact_number' => ['required', 'string', 'max:32'],
                ])->validate();
            }
            $selected = array_key_exists('roles', $data) ? $this->selectedRoles($data['roles']) : null;
            $current = $locked->roles()->whereNotIn('name', ['staff', 'superadmin'])->pluck('name')->all();
            if (! $admin) {
                if ($selected !== null) {
                    sort($selected);
                    sort($current);
                    abort_unless($selected === $current, 403);
                }
                abort_if(array_key_exists('isActive', $data) && (bool) $data['isActive'] !== (bool) $locked->is_active, 403);
            } else {
                abort_if(filled($locked->citizen_uuid) && ($selected || $locked->isInternal()), 403, 'Portal accounts cannot become staff.');
                Validator::make($data, ['isActive' => ['sometimes', 'boolean']])->validate();
                $active = (bool) ($data['isActive'] ?? $locked->is_active);
                if (! $active && $locked->is_active) {
                    abort_if($owner, 403, 'You cannot deactivate your own account.');
                    abort_if($administrators->contains('id', $locked->id) && $administrators->where('is_active', true)->count() <= 1,
                        403, 'The last active superadmin cannot be deactivated.');
                    if ($locked->isInternal()) {
                        app(SuperadminMfa::class)->protect($actor, (string) ($data['mfa_code'] ?? ''), fn () => null);
                        app(SuperadminMfa::class)->revoke($locked);
                        SuperadminMfaCredential::where('user_id', $locked->id)->get()->each(
                            fn ($credential) => $credential->forceFill(['version' => (string) Str::uuid()])->save()
                        );
                        app(SuperadminMfa::class)->audit($actor, 'Internal account disabled', ['target_id' => $locked->id]);
                    }
                }
                if ($active && ! $locked->is_active) {
                    app(AuthenticationAudit::class)->record($locked, 'Internal account reactivated', actor: $actor);
                }
                $attributes['is_active'] = $active;
                if ($selected !== null) {
                    $protected = $locked->roles()->whereIn('name', ['staff', 'superadmin'])->pluck('name')->all();
                    if ($locked->isInternal() || $selected) {
                        $protected[] = 'staff';
                    }
                    $roles = array_unique(array_merge($protected, $selected));
                    if ($locked->roles->pluck('name')->sort()->values()->all() !== collect($roles)->sort()->values()->all()) {
                        app(AuthenticationAudit::class)->record($locked, 'Internal account roles changed', actor: $actor);
                    }
                    $locked->syncRoles($roles);
                }
            }
            $locked->update($attributes);

            return $locked;
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
            app(AuthenticationAudit::class)->record($user, 'Password change failed', 'failure', actor: $user, credential: false);
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
                activity('account-management')->causedBy($locked)->performedOn($locked)
                    ->withProperties(['method' => 'authenticated_password_change'])
                    ->log('Owner changed password');
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

    public function setSuperadmin(User $target, bool $grant, #[\SensitiveParameter] string $code): User
    {
        app(AccountAccess::class)->assertWritable();

        return DB::transaction(function () use ($target, $grant, $code) {
            $administrators = User::whereHas('roles', fn ($q) => $q->where('name', 'superadmin'))->orderBy('id')->lockForUpdate()->get();
            $locked = User::whereKey($target->id)->lockForUpdate()->firstOrFail();
            $actor = app(AccountAccess::class)->assertWritable();
            abort_unless($actor->hasRole('superadmin') && $actor->id !== $locked->id, 403);
            abort_unless(blank($locked->citizen_uuid) && $locked->isInternal(), 403, 'Only internal accounts can be superadmins.');
            abort_if($grant && ! $locked->is_active, 403, 'Activate this account before promotion.');
            abort_if($locked->hasRole('superadmin') === $grant, 409, 'This account already has the requested role state.');
            abort_if(! $grant && $locked->is_active && $administrators->where('is_active', true)->count() <= 1, 403, 'The last active superadmin cannot be demoted.');

            return app(SuperadminMfa::class)->protect($actor, $code, function () use ($locked, $actor, $grant) {
                if ($grant) {
                    $locked->assignRole('superadmin');
                } else {
                    $locked->removeRole('superadmin');
                }
                $mfa = app(SuperadminMfa::class);
                $mfa->revoke($locked);
                $credential = $mfa->credential($locked);
                $credential->forceFill(['version' => (string) Str::uuid()])->save();
                $mfa->audit($actor, $grant ? 'Superadmin role granted' : 'Superadmin role removed', ['target_id' => $locked->id]);

                return $locked;
            });
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
