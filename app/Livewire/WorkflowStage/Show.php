<?php

namespace App\Livewire\WorkflowStage;

use App\Models\WorkflowStage;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Show extends Component
{
    public WorkflowStage $stage;

    #[Validate('required|string|max:255')]
    public string $name;

    #[Validate('required|string|max:255')]
    public string $description;

    public function mount(WorkflowStage $stage)
    {
        $this->stage = $stage;
        $this->name = $this->stage->name;
        $this->description = $this->stage->description;
    }

    public function save(string $uuid)
    {
        $validated = $this->validate();

        WorkflowStage::where('uuid', $uuid)
            ->update($validated);
            
        session()->flash('Successfully updated stage information');
    }

    public function render()
    {
        return view('livewire.workflow-stage.show');
    }
}
