<?php

namespace App\Livewire\Rejection;

use App\Models\Application;
use App\Models\Rejection;
use App\Traits\Livewire\HasPlaceholder;
use Livewire\Component;

class Show extends Component
{
    use HasPlaceholder;

    public Application $application;

    public Rejection $rejection;

    public function mount(Application $application)
    {
        $this->application = $application;
        $this->rejection = $application->rejection;
    }

    public function render()
    {
        return view('livewire.rejection.show');
    }
}
