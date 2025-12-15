<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Spatie\Activitylog\Models\Activity;

class trackingActivityCalendar extends Component
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

        $logs = Activity::where('description', 'like', 'Burial Assistance Request tracked by guest')->get();

        $schedule = $logs->map(function ($log) {
            return [
                'title' => json_encode($log->properties),
                'date' => $log->created_at->toDateString(),
            ];
        });

        return view('components.tracking-activity-calendar', compact(
            'schedule'
        ));
    }
}
