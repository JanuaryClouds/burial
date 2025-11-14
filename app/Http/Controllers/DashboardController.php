<?php

namespace App\Http\Controllers;

use App\Http\Requests\BurialServiceRequest;
use App\Models\Deceased;
use App\Services\ProcessLogService;
use App\Models\Barangay;
use App\Models\BurialAssistance;
use App\Models\Claimant;
use App\Models\ProcessLog;
use Illuminate\Http\Request;
use App\Models\BurialAssistanceRequest;
use App\Models\BurialServiceProvider;
use App\Models\BurialService;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected $processLogService;

    public function index()
    {
        $lastLogs = ProcessLog::with('burialAssistance')->where('added_by', auth()->user()->id)->latest()->limit(2)->get();
        $pendingApplicationsCount = BurialAssistance::where('status', 'pending')->get()->count();
        $processingApplicationsCount = BurialAssistance::where('status', 'processing')->get()->count();
        $approvedApplicationsCount = BurialAssistance::where('status', 'approved')->get()->count();
        $totalApplications = BurialAssistance::where(function ($query) {
            $query->where('status', '!=', 'rejected');
            $query->where('status', '!=', 'released');
        })->count();

        $cardData = [
            [
                'label' => 'Pending Applications',
                'bg' => 'bg-warning',
                'icon' => 'fa-hourglass',
                'count' => $pendingApplicationsCount,
            ],
            [
                'label' => 'Processing Applications',
                'bg' => 'bg-primary',
                'icon' => 'fa-rotate-right',
                'count' => $processingApplicationsCount,
            ],
            [
                'label' => 'Approved Applications',
                'bg' => 'bg-success',
                'icon' => 'fa-circle-check',
                'count' => $approvedApplicationsCount,
            ],
            [
                'label' => 'Total Applications',
                'bg' => 'bg-info',
                'icon' => 'fa-equals',
                'count' => $totalApplications,
            ],
        ];
        return view('dashboard', compact(
            'cardData',
            'lastLogs'
        ));
    }

    public function user()
    {
        return view('user.dashboard');
    }

    public function trackerEvents() {
        $logs = Activity::where('description', 'like', 'Burial Assistance Request tracked by guest')->get();

        $events = $logs->map(function ($log) {
            return [
                'title' => $log->properties,
                'date' => $log->created_at->toDateString()
            ];         
        });

        return response()->json($events);
    }
}