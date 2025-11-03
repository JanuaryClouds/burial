<?php

namespace App\View\Components;

use App\Models\BurialAssistance;
use App\Models\Claimant;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class applicationBarangayChart extends Component
{

    public $applications;

    /**
     * Create a new component instance.
     */
    public function __construct($applications = null)
    {
        if (empty($applications) || (is_iterable($applications) && count($applications) == 0)) {
            $this->applications = Claimant::selectRaw('barangay_id, COUNT(*) as total')
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
            return;
        } else {
            $claimants = collect();
            foreach ($applications as $application) {
                if ($application->newClaimant) {
                    $claimants->push($application->newClaimant);
                } elseif ($application->claimant) {
                    $claimants->push($application->claimant);
                }
            }

            $this->applications = $claimants
                ->groupBy('barangay_id')
                ->map(function ($item) {
                    return [
                        'barangay' => $item->first()->barangay ? $item->first()->barangay->name : 'Unknown',
                        'count' => $item->count(),
                    ];
                });
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.application-barangay-chart', ['applications' => $this->applications]);
    }
}
