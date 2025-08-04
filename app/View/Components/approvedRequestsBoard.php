<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\burialAssistanceRequest;

class approvedRequestsBoard extends Component
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
        $waitingBurials = burialAssistanceRequest::getApprovedAssistanceRequestsByDate('waiting');
        $onGoingBurials = burialAssistanceRequest::getApprovedAssistanceRequestsByDate('on-going');
        $completedBurials = burialAssistanceRequest::getApprovedAssistanceRequestsByDate('completed');
        return view('components.approved-requests-board', compact('waitingBurials', 'onGoingBurials', 'completedBurials'));
    }
}
