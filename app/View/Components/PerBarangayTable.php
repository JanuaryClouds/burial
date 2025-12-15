<?php

namespace App\View\Components;

use App\Models\Deceased;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PerBarangayTable extends Component
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
        $deceasedPerBarangay = Deceased::with('barangay')
            ->get()
            ->groupBy(fn ($item) => $item->barangay->name);

        return view('components.per-barangay-table', compact('deceasedPerBarangay'));
    }
}
