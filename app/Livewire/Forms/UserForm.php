<?php

namespace App\Livewire\Forms;

use App\Models\User;
use Livewire\Attributes\Validate;
use Livewire\Form;

class UserForm extends Form
{
    #[Validate('required|string|max:255')]
    public ?string $firstName = null;

    #[Validate('nullable|string|max:255')]
    public ?string $middleName = null;

    #[Validate('required|string|max:255')]
    public ?string $lastName = null;

    #[Validate('nullable|string|max:255')]
    public ?string $suffix = null;

    #[Validate('required|email|unique:users,email')]
    public ?string $email = null;

    #[Validate('required|string|max:255')]
    public ?string $password = null;

    #[Validate('required|string|same:password')]
    public ?string $confirmPassword = null;

    #[Validate('required|string|regex:^09\d{9}$')]
    public ?string $contactNumber = null;

    #[Validate('nullable|array|in:roles,id')]
    public ?array $roleIds = null;

    #[Validate('nullable|boolean')]
    public bool $isActive = true;

    public function setUser(User $user)
    {
        $this->firstName = $user->first_name;
        $this->middleName = $user->middle_name;
        $this->lastName = $user->last_name;
        $this->suffix = $user->suffix;
        $this->email = $user->email;
        $this->contactNumber = $user->contact_number;
        $this->roleIds = $user->roles->pluck('id')->toArray();
        $this->isActive = $user->is_active;
    }
}
