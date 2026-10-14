<?php

namespace App\Livewire\Role;

use App\Services\RoleService;
use App\Services\ActivityLoggerService;
use App\Traits\Livewire\HasPlaceholder;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class Edit extends Component
{
    use HasPlaceholder;

    public ?Role $role = null;

    #[Validate]
    public ?string $name = null;

    #[Validate('nullable|array')]
    public array $permissions = [];

    public array $permissionsOptions = [];

    public function mount(Role $role)
    {
        $this->role = $role;
        
        $this->permissionsOptions = Permission::orderBy('name', 'asc')
            ->get()
            ->mapWithKeys(fn($permission) => [$permission->name => $permission->name])
            ->toArray();

        $this->name = $role->name;
        $this->permissions = $role->permissions->pluck('name')
            ->mapWithKeys(fn($permission) => [$permission => $permission])
            ->toArray();
    }

    protected function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::unique('roles')->ignore($this->role->id),
            ],
        ];
    }

    public function clearPermissions()
    {
        $this->permissions = [];
    }

    public function save()
    {
        try {
            $this->validate();
        } catch (\Exception $e) {
            $this->dispatch('notification:toast', [
                'type' => 'error',
                'text' => app()->hasDebugModeEnabled() ? $e->getMessage() : config('constants.errors.validation')
            ]);
        }

        try {
            $role = app(RoleService::class)->update($this->all(), $this->role);

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
