<?php

namespace App\Livewire\Forms;

use App\Models\Application;
use Livewire\Attributes\Validate;
use Livewire\Form;

class ClosureForm extends Form
{
    #[Validate('required|string|max:65535')]
    public ?string $reason = null;

    public function setClosure(Application $application): void
    {
        $this->reason = $application->closure?->reason;
    }
}
