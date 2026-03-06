<?php

namespace App\Http\Controllers;

use App\Models\ProcessLog;
use App\Services\DatatableService;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    protected $datatableServices;

    public function __construct(DatatableService $datatableService)
    { 
        $this->datatableServices = $datatableService;
    }
    public function index()
    {
        $logs = Activity::with('causer')
            ->select('id', 'description', 'causer_type', 'causer_id', 'properties', 'created_at')
            ->get()
            ->map(function ($log) {
                $properties = $log->properties?->toArray() ?? [];
                $ip = $properties['ip'] ?? 'N/A';
                $browser = $properties['browser'] ?? 'N/A';
                return [
                    'id' => $log->id,
                    'description' => $log->description,
                    'caused_by' => $log->causer?->first_name ?? '',
                    'IP_address' => $ip,
                    'browser' => $browser,
                    'created_at' => $log->created_at->format('Y-m-d H:i:s'),
                ];
            });
        $columns = $this->datatableServices->getColumns($logs, ['id']);

        if (request()->expectsJson()) {
            return response()->json([
                'data' => $logs->values(),
            ]);
        }

        return view('logs.index', compact('logs', 'columns'));
    }
}
