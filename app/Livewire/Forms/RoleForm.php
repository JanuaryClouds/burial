<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;
use Spatie\Permission\Models\Role;

class RoleForm extends Form
{
    #[Validate('required|string|max:255|unique:roles,name')]
    public ?string $name = null;

    #[Validate('nullable|array')]
    public array $permissions = [];
}
