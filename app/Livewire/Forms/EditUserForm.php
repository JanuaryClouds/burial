<?php

namespace App\Livewire\Forms;

use App\Models\User;
use Livewire\Attributes\Validate;
use Livewire\Form;

class EditUserForm extends Form
{
    #[Validate('required|string|max:255')]
    public ?string $firstName = null;

    #[Validate('nullable|string|max:255')]
    public ?string $middleName = null;

    #[Validate('required|string|max:255')]
    public ?string $lastName = null;

    #[Validate('nullable|string|max:255')]
    public ?string $suffix = null;

    #[Validate('required|email|max:255')]
    public ?string $email = null;

    #[Validate('required|string|max:255')]
    public ?string $contactNumber = null;

    #[Validate('nullable|boolean')]
    public bool $isActive = true;

    #[Validate('nullable|string|max:255|password')]
    public ?string $currentPassword = null;

    #[Validate('nullable|string|max:255|required_with:currentPassword')]
    public ?string $newPassword = null;

    #[Validate('nullable|string|max:255|same:newPassword|required_with:currentPassword')]
    public ?string $confirmNewPassword = null;

    #[Validate('nullable|array')]
    public array $roles = [];

    public function setUser(User $user)
    {
        $this->firstName = $user->first_name;
        $this->middleName = $user->middle_name;
        $this->lastName = $user->last_name;
        $this->suffix = $user->suffix;
        $this->email = $user->email;
        $this->contactNumber = $user->contact_number;
        $this->isActive = $user->is_active;
        $this->roles = $user->roles()->whereNotIn('name', ['staff', 'superadmin'])->pluck('name')
            ->mapWithKeys(fn ($role) => [$role => $role])
            ->toArray();
    }
}
