<?php

namespace App\View\Components;

use App\Models\BurialAssistance;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class applicationsModalLoader extends Component
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
        $applications = BurialAssistance::select(
            'id',
            'tracking_no',
            'application_date',
            'deceased_id',
            'claimant_id',
            'amount',
            'funeraria',
            'status',
        )
            ->get();
        return view('components.applications-modal-loader', compact('applications'));
    }
}
