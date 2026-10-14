<?php

namespace App\Livewire\Role;

use App\Livewire\Forms\RoleForm;
use App\Services\ActivityLoggerService;
use App\Services\RoleService;
use App\Traits\Livewire\HasPlaceholder;
use Livewire\Component;
use Spatie\Permission\Models\Permission;

class Create extends Component
{
    use HasPlaceholder;

    public array $permissions = [];

    public RoleForm $form;

    public function mount()
    {
        $this->permissions = Permission::query()
            ->whereNotIn('name', ['workflow.*'])
            ->get()
            ->mapWithKeys(fn($permission) => [$permission->name => $permission->name])
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
            $role = app(RoleService::class)->store($this->form->all());

            $this->dispatch('notification:alert', [
                'type' => 'success',
                'text' => "Successfully created new role"
            ]);

            ActivityLoggerService::logSuccess('Successfully created new role', [
                'id' => $role->id,
            ]);

            $this->form->reset();
        } catch (\Throwable $th) {
            $this->dispatch('notification:alert', [
                'type' => 'error',
                'text' => app()->hasDebugModeEnabled() ? $th->getMessage() : config('constants.errors.unknown')
            ]);

            ActivityLoggerService::logException($th, "Failed to save new role");

            report($th);
        }
    }

    public function render()
    {
        return view('livewire.role.create');
    }
}
