<?php

namespace App\Livewire\Closure;

use App\Models\Application;
use App\Traits\Livewire\GuardsApplicationView;
use App\Traits\Livewire\HasPlaceholder;
use Livewire\Component;

class Show extends Component
{
    use GuardsApplicationView;
    use HasPlaceholder;

    public ?Application $application = null;

    public function mount(?Application $application)
    {
        $this->application = $application->load(['closure', 'closure.closedBy']);
    }

    public function render()
    {
        return view('livewire.closure.show');
    }
}
