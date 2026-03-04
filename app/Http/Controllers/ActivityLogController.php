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
        $logs = Activity::select('id', 'description', 'causer_type', 'causer_id', 'properties', 'created_at')->get()
            ->map(function ($log) {
                $ip = $log->properties?->toArray()['ip'] ?? 'N/A';
                $browser = $log->properties?->toArray()['browser'] ?? 'N/A';
                return [
                    'id' => $log->id,
                    'description' => $log->description,
                    'caused_by' => auth()->user()->where('id', $log->causer_id)->first()->first_name ?? '',
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
