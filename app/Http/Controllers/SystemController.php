<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use App\Services\DataTableService;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;

class SystemController extends Controller
{
    public function __construct(
        protected DataTableService $dataTableServices
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        if (! $user || ! $user->hasRole('superadmin')) {
            return redirect()->back()->with('warning', 'You do not have access to this page.');
        }

        return view('system.index', [
            'pageTitle' => 'System',
        ]);
    }

    public function settings()
    {
        return view('system.settings', [
            'pageTitle' => 'System Settings',
            'settings' => SystemSetting::first(),
        ]);
    }

    public function logs()
    {
        $logs = Activity::latest()->get()
            ->map(function ($log) {
                $properties = $log->properties;

                return [
                    'id' => $log->id,
                    'description' => $log->description,
                    'logged_at' => $log->created_at->format('Y-m-d H:i:s'),
                    'show_route' => route('system.logs.show', $log->id),
                ];
            });

        $columns = $this->dataTableServices->getColumns($logs);

        if (request()->expectsJson()) {
            return $this->dataTableServices->ajax($logs);
        }

        return view('system.log.index', [
            'pageTitle' => 'System Logs',
            'logs' => $logs,
            'columns' => $columns,
        ]);
    }

    public function showLog(Activity $log)
    {
        return view('system.log.show', [
            'pageTitle' => $log->created_at->format('F j, Y g:i A'),
            'log' => $log,
        ]);
    }
}
