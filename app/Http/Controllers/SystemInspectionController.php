<?php

namespace App\Http\Controllers;

use App\Services\ConfigurationReadinessService;
use App\Services\SystemDiagnosticsService;
use Illuminate\Contracts\View\View;

class SystemInspectionController extends Controller
{
    public function readiness(ConfigurationReadinessService $inspection): View
    {
        return view('system.inspection', ['pageTitle' => 'Configuration Readiness', 'rows' => $inspection->inspect(), 'packages' => null, 'inspectedAt' => now()]);
    }

    public function diagnostics(SystemDiagnosticsService $inspection): View
    {
        $results = $inspection->inspect();

        return view('system.inspection', ['pageTitle' => 'System Diagnostics', 'rows' => $results['checks'], 'packages' => $results['packages'], 'inspectedAt' => now()]);
    }
}
