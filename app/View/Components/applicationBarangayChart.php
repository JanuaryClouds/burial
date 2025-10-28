<?php

namespace App\View\Components;

use App\Models\BurialAssistance;
use App\Models\Claimant;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class applicationBarangayChart extends Component
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
        $applications = Claimant::selectRaw('barangay_id, COUNT(*) as total')
            ->with('barangay')
            ->groupBy('barangay_id')
            ->whereYear('created_at', now()->year)
            ->get()
            ->map(function ($item) {
                return [
                    'barangay' => $item->barangay ? $item->barangay->name : 'Unknown',
                    'count' => $item->total,
                ];
            });
        return view('components.application-barangay-chart', compact('applications'));
    }
}
