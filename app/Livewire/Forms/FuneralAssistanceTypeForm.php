<?php

namespace App\Livewire\Forms;

use App\Models\FuneralAssistanceType;
use Livewire\Attributes\Validate;
use Livewire\Form;

class FuneralAssistanceTypeForm extends Form
{
    #[Validate('required|string|max:255|unique:funeral_assistance_types,name')]
    public ?string $name = null;

    #[Validate('required|string|max:65535')]
    public ?string $description = null;

    #[Validate('nullable|string|exists:workflows,uuid')]
    public ?string $workflowUuid = null;

    public function setFuneralAssistanceType(FuneralAssistanceType $funeralAssistanceType)
    {
        $this->name = $funeralAssistanceType->name;
        $this->description = $funeralAssistanceType->description;
        $this->workflowUuid = $funeralAssistanceType->workflow_uuid;
    }
}
