<?php

namespace App\Livewire\User;

use App\Livewire\Forms\EditUserForm;
use App\Models\User;
use App\Services\ActivityLoggerService;
use App\Services\Auth\AccountAccess;
use App\Services\Auth\UserService;
use App\Traits\Livewire\HasPlaceholder;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class Edit extends Component
{
    use HasPlaceholder;

    public function hydrate(): void
    {
        $this->authorize('update', $this->user);
    }

    public EditUserForm $form;

    public ?User $user = null;

    public array $roleOptions = [];

    public string $mfaCode = '';

    public function changeSuperadmin(bool $grant): void
    {
        try {
            app(UserService::class)->setSuperadmin($this->user, $grant, $this->mfaCode);
            $this->user->refresh();
            $this->dispatch('notification:alert', [
                'type' => 'success',
                'title' => $grant ? 'Superadmin access granted' : 'Superadmin access removed',
                'text' => $grant ? 'This account now has superadmin access.' : 'This account no longer has superadmin access.',
            ]);
        } finally {
            $this->reset('mfaCode');
        }
    }

    public function mount(User $user)
    {
        $this->authorize('update', $user);
        $this->user = $user;
        if (auth()->id() === $user->id) {
            $this->form->setUser($user);
        } else {
            $this->form->isActive = $user->is_active;
            $this->form->roles = $user->roles()->whereNotIn('name', ['staff', 'superadmin'])->pluck('name')->mapWithKeys(fn ($role) => [$role => $role])->all();
        }
        $this->roleOptions = Role::where('guard_name', 'web')->pluck('name')
            ->filter(fn ($role) => ! in_array($role, ['staff', 'superadmin']))
            ->sort()
            ->mapWithKeys(fn ($role) => [$role => $role])
            ->toArray();
    }

    public function save()
    {
        app(AccountAccess::class)->assertWritable();
        $owner = auth()->id() === $this->user->id;
        try {
            if ($owner) {
                $this->form->validate();
            } else {
                foreach (['firstName', 'middleName', 'lastName', 'suffix', 'email', 'contactNumber'] as $field) {
                    abort_if($this->form->$field !== null, 403, 'Only the owner can edit their profile.');
                }
                $this->validate(['form.isActive' => 'boolean', 'form.roles' => 'array']);
            }
        } catch (\Exception $e) {
            $this->reset('mfaCode');
            $this->dispatch('notification:toast', [
                'type' => 'error',
                'text' => app()->hasDebugModeEnabled() ? $e->getMessage() : config('constants.errors.validation'),
            ]);

            return;
        }

        try {
            app(UserService::class)->update(
                ($owner ? $this->form->all() : ['isActive' => $this->form->isActive, 'roles' => $this->form->roles]) + ['mfa_code' => $this->mfaCode],
                $this->user
            );

            $this->dispatch('notification:alert', [
                'type' => 'success',
                'text' => 'Successfully updated user',
            ]);

            ActivityLoggerService::logSuccess('Successfully updated user', [
                'id' => $this->user->id,
            ]);
        } catch (\Illuminate\Validation\ValidationException $exception) {
            throw $exception;
        } catch (\Throwable $th) {
            $this->dispatch('notification:alert', [
                'type' => 'error',
                'text' => app()->hasDebugModeEnabled() ? $th->getMessage() : config('constants.errors.unknown'),
            ]);

            ActivityLoggerService::logException($th, 'Failed to updated user');

            report($th);
        } finally {
            $this->reset('mfaCode');
        }
    }

    public function render()
    {
        return view('livewire.user.edit');
    }
}
