<?php

namespace App\Livewire\Cancellation;

use App\Models\Application;
use App\Models\Cancellation;
use App\Traits\Livewire\HasPlaceholder;
use Livewire\Component;

class Show extends Component
{
    use HasPlaceholder;

    public Application $application;

    public Cancellation $cancellation;

    public function mount(Application $application)
    {
        $this->application = $application;
        $this->cancellation = $application->cancellation;
    }

    public function render()
    {
        return view('livewire.cancellation.show');
    }
}
