<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class ReferralForm extends Form
{
    #[Validate('required|string|max:255')]
    public ?string $referred_to = null;

    #[Validate('required|string|max:65535')]
    public ?string $reason = null;
}
