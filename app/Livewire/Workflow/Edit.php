<?php

namespace App\Livewire\Workflow;

use App\Models\Workflow;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Edit extends Component
{
    public Workflow $workflow;

    #[Validate]
    public string $name;

    #[Validate('required|string|max:65535')]
    public string $description;

    public function mount(Workflow $workflow)
    {
        $this->workflow = $workflow;
        $this->name = $workflow->name;
        $this->description = $workflow->description;
    }

    public function update()
    {
        
    }

    public function render()
    {
        return view('livewire.workflow.edit');
    }
}
