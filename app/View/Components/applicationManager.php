<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class applicationManager extends Component
{
    public $processLogs;
    public $application;
    /**
     * Create a new component instance.
     */
    public function __construct($application)
    {
        $this->application = $application;
        $this->processLogs = $application ? $application->processLogs()->latest()->get() : collect();
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {

        return view('components.application-manager');
    }
}
