<?php

namespace App\View\Components;

use App\Models\BurialAssistance;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\ProcessLog;

class assistanceProcessTracker extends Component
{
    public $processLogs;
    public $burialAssistance;
    public $claimantChanges;
    public $claimants;
    /**
     * Create a new component instance.
     */
    public function __construct($burialAssistance)
    {
        $this->burialAssistance = $burialAssistance;
        $this->processLogs = $burialAssistance
            ? $burialAssistance->processLogs()->oldest()->get()
            : collect();
        $this->claimantChanges = $burialAssistance ? $burialAssistance->claimantChanges()->latest()->get() : collect();
        $this->claimants = $burialAssistance ? $burialAssistance->claimant()->get() : collect();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.assistance-process-tracker');
    }
}
