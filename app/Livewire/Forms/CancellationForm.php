<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class CancellationForm extends Form
{
    #[Validate('required|string|max:65535')]
    public ?string $reason = null;
}
