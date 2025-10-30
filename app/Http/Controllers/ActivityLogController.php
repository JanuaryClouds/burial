<?php

namespace App\Http\Controllers;

use App\Models\ProcessLog;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index()
    {
        $logs = Activity::select('id', 'description', 'causer_type', 'causer_id', 'properties', 'created_at')->get();
        $processLogs = ProcessLog::select('id', 'burial_assistance_id', 'loggable_id', 'loggable_type', 'date_in', 'date_out', 'added_by')->get();
        return view('logs.index');
    }
}
