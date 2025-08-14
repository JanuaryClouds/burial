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
        $allRequests = BurialAssistanceRequest::where(function ($query) {
            $query->where("status", "pending")
                ->where("start_of_burial", ">", now("Asia/Manila"));
        });
        return view('components.burial-assistance-requests-table', compact('allRequests'));
    }
}
