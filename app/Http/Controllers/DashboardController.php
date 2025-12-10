<?php

namespace App\Http\Controllers;

use App\Http\Requests\BurialServiceRequest;
use App\Models\Deceased;
use App\Services\ProcessLogService;
use App\Models\Client;
use App\Models\Barangay;
use App\Models\BurialAssistance;
use App\Models\FuneralAssistance;
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
        $page_title = 'Dashboard';
        $lastLogs = ProcessLog::with('burialAssistance')->where('added_by', auth()->user()->id)->latest()->limit(2)->get();
        $pendingBurialAssistance = BurialAssistance::where(function ($query) {
            $query
                ->orWhere('status', 'pending')
                ->orWhere('status', 'processing');
        })->count();
        $pendingFuneralAssistance = FuneralAssistance::where(function ($query) {
            $query->where('approved_at', null);
            $query->where('forwarded_at', null);
        })->count();
        $processingApplicationsCount = BurialAssistance::where('status', 'processing')->get()->count();
        $approvedApplicationsCount = BurialAssistance::where('status', 'approved')->get()->count();
        $totalApplications = BurialAssistance::where(function ($query) {
            $query->where('status', '!=', 'rejected');
            $query->where('status', '!=', 'released');
        })->count();

        $pendingClients = Client::where(function ($query) {
            $query->where('created_at', '>=', Carbon::now()->subDays(7));
        })->count();

        $cardData = [
            [
                'label' => 'Clients this week',
                'icon' => 'ki-chart-line',
                'pathsCount' => 2,
                'link' => route('client.index'),
                'count' => $pendingClients,
            ],
            [
                'label' => 'Pending Burial Assistance',
                'icon' => 'ki-timer',
                'pathsCount' => 2,
                'link' => route('burial.index', ['status' => 'pending']),
                'count' => $pendingBurialAssistance,
            ],
            [
                'label' => 'Pending Funeral Assistance',
                'icon' => 'ki-watch',
                'pathsCount' => 2,
                'link' => route('funeral.index'),
                'count' => $pendingFuneralAssistance,
            ],
        ];
        return view('dashboard', compact(
            'cardData',
            'lastLogs',
            'page_title'
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