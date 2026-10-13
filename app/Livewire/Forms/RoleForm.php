<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;
use Spatie\Permission\Models\Role;

class RoleForm extends Form
{
    #[Validate('required|string|max:255|unique:roles,name')]
    public ?string $name = null;

    #[Validate('nullable|array|exists:permissions,id')]
    public ?array $permissions = [];

    public function setRole(Role $role)
    {
        $this->name = $role->name;
        $this->permissions = $role->permissions()->pluck('id')->toArray();
    }
}
