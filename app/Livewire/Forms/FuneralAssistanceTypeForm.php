<?php

namespace App\Livewire\Forms;

use App\Models\FuneralAssistanceType;
use App\Models\Workflow;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Form;

class FuneralAssistanceTypeForm extends Form
{
    public ?FuneralAssistanceType $funeralAssistanceType = null;

    #[Validate]
    public ?string $name = null;

    #[Validate]
    public ?string $description = null;

    #[Validate]
    public ?string $workflowUuid = null;

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('funeral_assistance_types', 'name')->ignore($this->funeralAssistanceType)],
            'description' => ['required', 'string', 'max:65535'],
            'workflowUuid' => [
                'nullable', 'uuid',
                function ($attribute, $value, $fail) {
                    if ($value && ! Workflow::selectable()->whereKey($value)->exists()) {
                        $fail('Choose the latest active, complete workflow version or clear the suggestion.');
                    }
                },
            ],
        ];
    }

    protected function messages(): array
    {
        return ['workflowUuid.exists' => 'Choose an active workflow or clear the assignment.'];
    }

    public function setFuneralAssistanceType(FuneralAssistanceType $funeralAssistanceType): void
    {
        $this->funeralAssistanceType = $funeralAssistanceType;
        $this->name = $funeralAssistanceType->name;
        $this->description = $funeralAssistanceType->description;
        $this->workflowUuid = $funeralAssistanceType->workflow_uuid;
    }
}
