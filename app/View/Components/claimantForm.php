<?php

namespace App\View\Components;

use App\Models\Barangay;
use App\Models\Relationship;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class claimantForm extends Component
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
        $relationships = Relationship::select('id', 'name')->get();
        $barangays = Barangay::select('id', 'name')->get();
        return view('components.claimant-form', compact(
            'relationships',
            'barangays'
        ));
    }
}
