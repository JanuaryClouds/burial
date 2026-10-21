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
        return view('system.log.index', [
            'pageTitle' => 'System Logs',
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
