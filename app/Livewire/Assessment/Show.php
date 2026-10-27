<?php

namespace App\Livewire\Assessment;

use App\Models\Assessment;
use App\Traits\Livewire\HasPlaceholder;
use Livewire\Component;

class Show extends Component
{
    use HasPlaceholder;

    public Assessment $assessment;

    public function mount(Assessment $assessment)
    {
        $this->assessment = $assessment;
    }

    public function render()
    {
        $application = $this->assessment->application()->firstOrFail();
        abort_unless(auth()->user()?->isInternal() && auth()->user()->can('view', $application), 403);
        abort_unless(auth()->user()->hasOfficeAccess() || $application->current_claimant_assignment_uuid === $this->assessment->claimant_assignment_uuid, 403);

        return view('livewire.assessment.show');
    }
}
