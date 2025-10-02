<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\Barangay;
use App\Models\Sex;
use App\Models\Religion;

class deceasedForm extends Component
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
        $sexes = Sex::select('id', 'name')->get();
        $religions = Religion::select('id', 'name')->get();
        return view('components.deceased-form', compact(
            'barangays',
            'sexes',
            'religions',
        ));
    }
}
