<?php

namespace App\Livewire\Forms;

use App\Models\ModeOfAssistance;
use Livewire\Attributes\Validate;
use Livewire\Form;

class ModeOfAssistanceForm extends Form
{
    #[Validate('required|string|max:255|unique:mode_of_assistances,name')]
    public ?string $name = null;

    public function setModeOfAssistance(ModeOfAssistance $modeOfAssistance)
    {
        $this->name = $modeOfAssistance->name;
    }
}
