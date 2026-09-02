<?php

namespace App\Livewire\Assessment;

use App\Models\Assessment;
use Livewire\Component;

class Show extends Component
{
    public Assessment $assessment;

    public function mount(Assessment $assessment)
    {
        $this->assessment = $assessment;
    }

    public function render()
    {
        return view('livewire.assessment.show');
    }
}
