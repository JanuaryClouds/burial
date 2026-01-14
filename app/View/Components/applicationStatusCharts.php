<?php

namespace App\View\Components;

use App\Models\BurialAssistance;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Str;

class applicationStatusCharts extends Component
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
        $applications = BurialAssistance::select('status')
            ->whereYear('created_at', now()->year)
            ->get()
            ->groupBy('status')
            ->map(function ($status) {
                return [
                    'status' => Str::title($status[0]['status']),
                    'count' => count($status),
                ];
            });

        // dd($applications);
        return view('components.application-status-charts', compact('applications'));
    }
}
