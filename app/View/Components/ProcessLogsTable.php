<?php

namespace App\View\Components;

use App\Models\ProcessLog;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ProcessLogsTable extends Component
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
        $processLogs = ProcessLog::select('id', 'burial_assistance_id', 'loggable_id', 'loggable_type', 'date_in', 'date_out', 'added_by')->get();
        return view('components.process-logs-table', compact('processLogs'));
    }
}
