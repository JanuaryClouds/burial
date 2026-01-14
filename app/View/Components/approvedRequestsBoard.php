<?php

namespace App\View\Components;

use App\Models\BurialAssistanceRequest;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

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
        $waitingBurials = BurialAssistanceRequest::getApprovedAssistanceRequestsByDate('waiting');
        $onGoingBurials = BurialAssistanceRequest::getApprovedAssistanceRequestsByDate('on-going');
        $completedBurials = BurialAssistanceRequest::getApprovedAssistanceRequestsByDate('completed');

        return view('components.approved-requests-board', compact('waitingBurials', 'onGoingBurials', 'completedBurials'));
    }
}
