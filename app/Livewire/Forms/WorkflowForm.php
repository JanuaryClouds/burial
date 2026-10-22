<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class WorkflowForm extends Form
{
    #[Validate('required|string|max:255')]
    public string $name;

    #[Validate('required|string|max:65535')]
    public string $description;
}
