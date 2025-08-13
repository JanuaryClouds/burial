<?php

namespace App\View\Components;

use App\Http\Requests\BurialAssistanceReqRequest;
use App\Http\Requests\BurialServiceRequest;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\BurialAssistanceRequest;

class burialCalendar extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $schedule = BurialAssistanceRequest::getBurialSchedules();
        return view('components.burial-calendar', compact('schedule'));
    }
}
