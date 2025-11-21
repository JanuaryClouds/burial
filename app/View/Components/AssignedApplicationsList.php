<?php

namespace App\View\Components;

use App\Models\BurialAssistance;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AssignedApplicationsList extends Component
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
        $applications = BurialAssistance::with([
            'deceased',
            'deceased.barangay',
            'claimant',
            'claimant.barangay',
            'claimantChanges',
            'claimantChanges.newClaimant',
            'claimantChanges.newClaimant.barangay',
            'processLogs',
            'user',
        ])
        ->where(function ($query) {
            $query->where('status', '!=', 'rejected')
                ->where('status', '!=', 'released')
                ->where('assigned_to', auth()->user()->id);   
        })->get();
        return view('components.assigned-applications-list', compact('applications'));
    }
}
