<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\BurialAssistanceRequest;

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
        $allRequests = BurialAssistanceRequest::whereNull('service_id')
            ->where('start_of_burial', '>', now('Asia/Manila'))
            ->get();
        return view('components.burial-assistance-requests-table', compact('allRequests'));
    }
}
