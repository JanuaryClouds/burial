<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\burialAssistanceRequest;

class burialAssistanceRequestsTable extends Component
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
        $allRequests = burialAssistanceRequest::getAllBurialAssistanceRequests();
        return view('components.burial-assistance-requests-table', compact('allRequests'));
    }
}
