<?php

namespace App\View\Components;

use App\Models\BurialAssistance;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class latestApplicationsTable extends Component
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
        $latestApplications = BurialAssistance::select(['id', 'deceased_id', 'claimant_id', 'tracking_no', 'funeraria', 'status', 'application_date', 'assigned_to', 'amount'])
            ->with(['deceased', 'claimant'])
            ->orderBy('application_date', 'asc')
            ->take(10)
            ->get();
        return view('components.latest-applications-table', compact(
            'latestApplications'
        ));
    }
}
