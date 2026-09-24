<?php

namespace App\Livewire\Referral;

use App\Models\Application;
use App\Models\Referral;
use App\Traits\Livewire\HasPlaceholder;
use Livewire\Component;

class Show extends Component
{
    use HasPlaceholder;

    public Application $application;

    public Referral $referral;

    public function mount(Application $application)
    {
        $this->application = $application;
        $this->referral = $application->referral;
    }

    public function render()
    {
        return view('livewire.referral.show');
    }
}
