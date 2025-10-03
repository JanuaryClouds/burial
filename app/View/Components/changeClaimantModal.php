<?php

namespace App\View\Components;

use App\Models\Barangay;
use App\Models\Relationship;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class changeClaimantModal extends Component
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
        $barangays = Barangay::select('id', 'name')->get();
        $relationships = Relationship::select('id', 'name')->get();
        return view('components.change-claimant-modal', compact(['barangays', 'relationships']));
    }
}
