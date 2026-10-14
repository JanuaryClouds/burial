<?php

namespace App\Livewire\Role;

use App\Livewire\Forms\RoleForm;
use App\Services\RoleService;
use App\Services\ActivityLoggerService;
use App\Traits\Livewire\HasPlaceholder;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class Edit extends Component
{
    use HasPlaceholder;

    public ?Role $role = null;

    public RoleForm $form;

    public array $permissions = [];

    public function mount(Role $role)
    {
        $this->role = $role;
        
        $this->permissions = Permission::whereNotIn('name', ['workflow.*'])
            ->get()
            ->mapWithKeys(fn($permission) => [$permission->name => $permission->name])
            ->toArray();

        $this->form->setRole($role);
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
            $role = app(RoleService::class)->update($this->form->all(), $this->role);

            $this->dispatch('notification:alert', [
                'type' => 'success',
                'text' => "Successfully updated role"
            ]);

            ActivityLoggerService::logSuccess('Successfully updated role', [
                'id' => $role->id,
            ]);
        } catch (\Throwable $th) {
            $this->dispatch('notification:alert', [
                'type' => 'error',
                'text' => app()->hasDebugModeEnabled() ? $th->getMessage() : config('constants.errors.unknown')
            ]);

            ActivityLoggerService::logException($th, "Failed to update role");

            report($th);
        }
    }

    public function render()
    {
        return view('livewire.role.edit');
    }
}
