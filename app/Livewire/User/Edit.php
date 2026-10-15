<?php

namespace App\Livewire\User;

use App\Livewire\Forms\EditUserForm;
use App\Livewire\Forms\UserForm;
use App\Models\User;
use App\Services\ActivityLoggerService;
use App\Services\Auth\UserService;
use App\Traits\Livewire\HasPlaceholder;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class Edit extends Component
{
    use HasPlaceholder;

    public EditUserForm $form;

    public ?User $user = null;

    public ?string $currentPassword = null;

    public array $roleOptions = [];

    public function mount(User $user)
    {
        $this->user = $user;
        $this->form->setUser($user);
        $this->roleOptions = Role::pluck('name')
            ->filter(fn($role) => !in_array($role, ['staff', 'superadmin']))
            ->sort()
            ->mapWithKeys(fn($role) => [$role => $role])
            ->toArray();
    }

    public function save()
    {
        try {
            $this->form->validate();
        } catch (\Exception $e) {
            $this->dispatch('notification:toast', [
                'type' => 'error',
                'text' => app()->hasDebugModeEnabled() ? $e->getMessage() : config('constants.errors.validation')
            ]);
        }

        try {
            app(UserService::class)->update(
                $this->form->all(),
                $this->user
            );

            $this->dispatch('notification:alert', [
                'type' => 'success',
                'text' => "Successfully created updated user"
            ]);

            ActivityLoggerService::logSuccess('Successfully updated user', [
                'id' => $this->user->id,
            ]);
        } catch (\Throwable $th) {
            $this->dispatch('notification:alert', [
                'type' => 'error',
                'text' => app()->hasDebugModeEnabled() ? $th->getMessage() : config('constants.errors.unknown')
            ]);

            ActivityLoggerService::logException($th, "Failed to updated user");

            report($th);
        }
    }

    public function render()
    {
        return view('livewire.user.edit');
    }
}
