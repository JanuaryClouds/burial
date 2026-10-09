<?php

namespace App\Livewire\Application;

use App\Models\Application;
use App\Traits\Livewire\HasPlaceholder;
use Livewire\Component;

class Close extends Component
{
    use HasPlaceholder;

    public Application $application;

    public ?string $closeMode = null;

    public array $closeModes = [
        'rejection' => 'Rejection',
        'referral' => 'Referral',
    ];

    public function mount(Application $application)
    {
        $this->application = $application;
    }

    public function render()
    {
        return view('livewire.application.close');
    }
}
