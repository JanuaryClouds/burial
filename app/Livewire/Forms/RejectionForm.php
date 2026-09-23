<?php

namespace App\Livewire\Forms;

use App\Models\Rejection;
use Livewire\Attributes\Validate;
use Livewire\Form;

class RejectionForm extends Form
{
    #[Validate('required|string|max:65535')]
    public ?string $reason = null;

    public function setRejection(Rejection $rejection)
    {
        $this->reason = $rejection->reason;
    }
}
