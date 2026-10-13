<?php

namespace App\Livewire\User;

use App\Livewire\Forms\UserForm;
use App\Services\ActivityLoggerService;
use App\Services\Auth\UserService;
use App\Traits\Livewire\HasPlaceholder;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class Create extends Component
{
    use HasPlaceholder;

    public UserForm $form;

    public ?array $roles = null;

    public function mount()
    {
        $this->roles = Role::select(['id', 'name'])
            ->get()
            ->mapWithKeys(fn($role) => [$role->id => $role->name])
            ->toArray();
    }

    public function clearForm()
    {
        $this->form->reset();
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
            $user = app(UserService::class)->store($this->form->all());

            $this->dispatch('notification:alert', [
                'type' => 'success',
                'text' => "Successfully created new user"
            ]);

            ActivityLoggerService::logSuccess('Successfully created new user', [
                'id' => $user->id,
            ]);

            $this->form->reset();
        } catch (\Throwable $th) {
            $this->dispatch('notification:alert', [
                'type' => 'error',
                'text' => app()->hasDebugModeEnabled() ? $th->getMessage() : config('constants.errors.unknown')
            ]);

            ActivityLoggerService::logException($th, "Failed to save new user");

            report($th);
        }
    }

    public function render()
    {
        return view('livewire.user.create');
    }
}
