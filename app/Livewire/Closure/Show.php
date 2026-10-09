<?php

namespace App\Livewire\Closure;

use App\Models\Application;
use App\Models\Closure;
use Livewire\Component;

class Show extends Component
{
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
