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
        return view('dashboard');
    }

    public function admin()
    {
        // ! Deprecated
        $perBarangay = Claimant::select('barangay_id', DB::raw('count(*) as total'))
            ->with('barangay')
            ->groupBy('barangay_id')
            ->get()
            ->map(function ($item) {
               return [
                   'name' => $item->barangay->name ?? 'Unknown',
                   'count' => $item->total,
               ];
            });
        
        // ! Deprecated
        $applicationsByBarangay = BurialAssistance::with(['claimant.barangay'])
            ->whereYear('created_at', now()->year)
            ->get()
            ->groupBy(fn($item) => $item->claimant->barangay->name);

        $lastLogs = ProcessLog::with('burialAssistance')->where('added_by', auth()->user()->id)->latest()->limit(2)->get();
        $pendingApplications = BurialAssistance::where('status', 'pending')->oldest()->limit(5)->get(); // ! Deparcated
        // ! Deparcated
        $monthlyActivity = ProcessLog::where('added_by', auth()->user()->id)
        ->select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('WEEK(created_at, 1) as week'),
            DB::raw('COUNT(*) as count')
        )
        ->groupBy('year', 'week')
        ->orderBy('year', 'asc')
        ->orderBy('week', 'asc')
        ->get()
        ->map(function ($item) {
            $start = Carbon::now()->setISODate($item->year, $item->week)->startOfWeek();
            $end = Carbon::now()->setISODate($item->year, $item->week)->endOfWeek();
            return [
                'week' => $start->format('M d') . ' - ' . $end->format('M d'),
                'count' => $item->count,
            ];
        });

        // ! Deprecated
        $userLogs = ProcessLog::where('added_by', auth()->user()->id)->select('created_at')
            ->get()
            ->pluck('created_at')
            ->sort()
            ->values();
        $updatesPerMinute = [];
        for ($i = 0; $i < $userLogs->count() - 1; $i++) {
            $start = Carbon::parse($userLogs[$i]);
            $end = Carbon::parse($userLogs[$i + 1]);
            $updatesPerMinute[] = $end->diffInMinutes($start);
        }

        $swaEncoded = BurialAssistance::where('encoder', auth()->user()->id)->count(); // ! Deprecated
        $logsAdded = ProcessLog::where('added_by', auth()->user()->id)->count(); // ! Deprecated
        $assignedApplications = BurialAssistance::where(function ($query) {
            $query->where('status', '!=', 'rejected')
                ->where('status', '!=', 'released')
                ->where('assigned_to', auth()->user()->id);   
        })->get();

        $pendingApplicationsCount = BurialAssistance::where('status', 'pending')->get()->count();
        $processingApplicationsCount = BurialAssistance::where('status', 'processing')->get()->count();
        $approvedApplicationsCount = BurialAssistance::where('status', 'approved')->get()->count();
        $totalApplications = BurialAssistance::where(function ($query) {
            $query->where('status', '!=', 'rejected');
        })->count();

        $cardData = [
            // ! Deprecated
            // [
            //     'label' => 'SWAs Encoded',
            //     'bg' => 'bg-primary',
            //     'icon' => 'fa-align-left',
            //     'count' => $swaEncoded,
            // ],
            // [
            //     'label' => 'Updates Added',
            //     'bg' => 'bg-secondary',
            //     'icon' => 'fa-list',
            //     'count' => $logsAdded,
            // ],
            // [
            //     'label' => 'Avg. Minutes per Update',
            //     'bg' => 'bg-success',
            //     'icon' => 'fa-bell-concierge',
            //     'count' => $updatesPerMinute > 0 ? number_format(collect($updatesPerMinute)->avg(), 2) . ' m' : '< 1 minute',
            // ],
            // [
            //     'label' => 'Assigned Applications',
            //     'bg' => 'bg-info',
            //     'icon' => 'fa-equals',
            //     'count' => $assignedApplications->count(),
            // ],
            [
                'label' => 'Pending Applications',
                'bg' => 'bg-primary',
                'icon' => 'fa-hourglass',
                'count' => $pendingApplicationsCount,
            ],
            [
                'label' => 'Processing Applications',
                'bg' => 'bg-secondary',
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

        return view('admin.dashboard', compact(
            'perBarangay', // ! Deprecated
            'lastLogs',
            'pendingApplications', // ! Deprecated
            'monthlyActivity', // ! Deprecated
            'applicationsByBarangay', // ! Deprecated
            'cardData',
            'assignedApplications',
        ));
    }

    public function user()
    {
        return view('user.dashboard');
    }

    public function superadmin()
    {
        // ! Deprecated
        $totalRequestLogs = Activity::where('description', 'like', 'Burial Assistance application %')->count();
        $firstRequest = Activity::where('description', 'like', 'Burial Assistance application %')->oldest()->first();
        $lastRequest = Activity::where('description', 'like', 'Burial Assistance application %')->latest()->first();

        if ($firstRequest && $lastRequest) {
            $months = Carbon::parse($firstRequest->created_at)->diffInMonths(Carbon::parse($lastRequest->created_at)) + 1;
            $avgRequestsPerMonth = $totalRequestLogs / $months;
        } else {
            $avgRequestsPerMonth = 0;
        }

        // ! Deprecated
        $totalTrackLogs = Activity::where('description', 'like', 'Burial Assistance tracked by guest')->count();
        $firstTrack = Activity::where('description', 'like', 'Burial Assistance tracked by guest')->oldest()->first();
        $lastTrack = Activity::where('description', 'like', 'Burial Assistance tracked by guest')->latest()->first();

        if ($firstTrack && $lastTrack) {
            $months = Carbon::parse($firstTrack->created_at)->diffInMonths(Carbon::parse($lastTrack->created_at)) + 1;
            $avgTracksPerMonth = $totalTrackLogs / $months;
        } else {
            $avgTracksPerMonth = 0;
        }
    
        // ! Deprecated
        $perBarangay = Claimant::select('barangay_id', DB::raw('count(*) as total'))
            ->with('barangay')
            ->groupBy('barangay_id')
            ->get()
            ->map(function ($item) {
               return [
                   'name' => $item->barangay->name ?? 'Unknown',
                   'count' => $item->total,
               ];
            });

        // ! Deprecated
        $applicationsThisYear = Deceased::select('barangay_id', DB::raw('count(*) as total'))
            ->with('barangay')
            ->whereYear('date_of_death', now()->year)
            ->groupBy('barangay_id')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->barangay->name ?? 'Unknown',
                    'count' => $item->total,
                ];
            });

        // ! Deprecated
        $applicationsThisMonth = Deceased::select('barangay_id', DB::raw('count(*) as total'))
            ->with('barangay')
            ->whereBetween('date_of_death', [now()->startOfMonth(), now()->endOfMonth()])
            ->groupBy('barangay_id')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->barangay->name ?? 'Unknown',
                    'count' => $item->total,
                ];
            });

        // ! Deprecated
        $applicationsThisWeek = Deceased::select('barangay_id', DB::raw('count(*) as total'))
            ->with('barangay')
            ->whereBetween('date_of_death', [now()->startOfWeek(), now()->endOfWeek()])
            ->groupBy('barangay_id')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->barangay->name ?? 'Unknown',
                    'count' => $item->total,
                ];
            });

        // ! Deprecated
        $applicationsThisDay = Deceased::select('barangay_id', DB::raw('count(*) as total'))
            ->with('barangay')
            ->where('date_of_death', now())
            ->groupBy('barangay_id')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->barangay->name ?? 'Unknown',
                    'count' => $item->total,
                ];
            });
        // dd($applicationsThisYear, $applicationsThisMonth, $applicationsThisWeek, $applicationsThisDay);
        
        // ! Deprecated
        $applicationsByBarangay = BurialAssistance::with(['claimant.barangay'])
            ->where(function ($query)  { 
                $query->where('status', '!=', 'rejected'); })
                ->whereYear('created_at', now()->year)
            ->get()
            ->groupBy(fn($item) => $item->claimant->barangay->name);

        $totalApplications = BurialAssistance::where(function ($query) {
            $query->where('status', '!=', 'rejected');
        })->count();

        $lastLogs = ProcessLog::orderBy('created_at', 'desc')->limit(5)->get(); // ! Deprecated
        $pendingApplications = BurialAssistance::where('status', 'pending')->oldest()->limit(10)->get(); // ! Deprecated

        // ! Deprecated
        $applicationAverages = BurialAssistance::with(['processLogs' => function ($query) {
            $query->orderBy('created_at', 'asc');
        }])
            ->get()
            ->map(function ($application) {
                $logs = $application->processLogs;
                if ($logs->count() < 2) {
                    return 0;
                }

                $diffs = [];
                for ($i = 0; $i < $logs->count() - 1; $i++) {
                    $start = Carbon::parse($logs[$i]->created_at);
                    $end = Carbon::parse($logs[$i + 1]->created_at);
                    $diffs[] = $start->diffInRealMinutes($end);
                }
                return collect($diffs)->avg();
            })
            ->filter()
            ->values();

        $globalAverageProcessing = $applicationAverages->avg();

        $pendingApplicationsCount = BurialAssistance::where('status', 'pending')->get()->count();
        $processingApplicationsCount = BurialAssistance::where('status', 'processing')->get()->count();
        $approvedApplicationsCount = BurialAssistance::where('status', 'approved')->get()->count();

        $cardData = [
            // ! Deprecated
            // [
            //     'label' => 'Applications/Month',
            //     'bg' => 'bg-primary',
            //     'icon' => 'fa-align-left',
            //     'count' => number_format($avgRequestsPerMonth, 2) . '/month',
            // ],
            // [
            //     'label' => 'Tracks/Month',
            //     'bg' => 'bg-secondary',
            //     'icon' => 'fa-list',
            //     'count' => number_format($avgTracksPerMonth, 2) . '/month',
            // ],
            [
                'label' => 'Pending Applications',
                'bg' => 'bg-primary',
                'icon' => 'fa-hourglass',
                'count' => $pendingApplicationsCount,
            ],
            [
                'label' => 'Processing Applications',
                'bg' => 'bg-secondary',
                'icon' => 'fa-rotate-right',
                'count' => $processingApplicationsCount,
            ],
            [
                'label' => 'Approved Applications',
                'bg' => 'bg-success',
                'icon' => 'fa-check-circle',
                'count' => $approvedApplicationsCount,
            ],
            [
                'label' => 'Total Applications',
                'bg' => 'bg-info',
                'icon' => 'fa-equals',
                'count' => $totalApplications,
            ],
            // ! Deprecated
            // [
            //     'label' => 'Avg. Processing Time per Update',
            //     'bg' => 'bg-warning',
            //     'icon' => 'fa-clock',
            //     'count' => $globalAverageProcessing ? number_format($globalAverageProcessing, 2) . ' m' : '<1 m',
            // ],
        ];

        return view('admin.dashboard', compact(
            'perBarangay', // ! Deprecated
            'applicationsByBarangay', // ! Deprecated
            
            // ! Deprecated
                'applicationsThisYear',
                'applicationsThisMonth',
                'applicationsThisWeek',
                'applicationsThisDay',
            // !
            'cardData',
            'lastLogs', // ! Deprecated
            'pendingApplications', // ! Deprecated
        ));
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