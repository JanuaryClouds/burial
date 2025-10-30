<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Spatie\Activitylog\Models\Activity;

class ActivityLogsTable extends Component
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
        $logs = Activity::select('id', 'description', 'causer_type', 'causer_id', 'properties', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('components.activity-logs-table', compact('logs'));
    }
}
