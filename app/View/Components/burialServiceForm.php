<?php

namespace App\View\Components;

use App\Models\Barangay;
use App\Models\BurialServiceProvider;
use App\Models\Relationship;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class burialServiceForm extends Component
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
        $barangays = Barangay::getAllBarangays();
        $providers = BurialServiceProvider::getAllProviders();
        $relationships = Relationship::getAllRelationships();
        return view('components.burial-service-form', compact('barangays', 'providers', 'relationships'));
    }
}
