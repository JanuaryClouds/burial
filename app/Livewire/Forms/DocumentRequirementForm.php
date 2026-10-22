<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class DocumentRequirementForm extends Form
{
    #[Validate('required|string|max:255|unique:document_requirements,name')]
    public string $name = '';

    #[Validate('nullable|string|max:65535')]
    public string $description = '';

    #[Validate('required|string|max:255')]
    public string $source = '';

    #[Validate('required|boolean')]
    public bool $isRequired = true;

    #[Validate('required|boolean')]
    public bool $forMuslim = false;

    #[Validate('required|boolean')]
    public bool $isActive = true;
}
