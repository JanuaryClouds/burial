<?php

namespace App\Http\Controllers;

use App\Http\Requests\BurialServiceRequest;
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
        
        $applicationsByBarangay = BurialAssistance::with(['claimant.barangay'])
            ->whereYear('created_at', now()->year)
            ->get()
            ->groupBy(fn($item) => $item->claimant->barangay->name);

        $lastLogs = ProcessLog::with('burialAssistance')->where('added_by', auth()->user()->id)->latest()->limit(4)->get();
        $pendingApplications = BurialAssistance::where('status', 'pending')->oldest()->limit(5)->get();
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

        $updatesPerHour = BurialAssistance::with(['processLogs' => function ($query) {
            $query->where('added_by', auth()->user()->id);
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
                    $diffs[] = $start->diffInHours($end);
                }
                return collect($diffs)->avg();
            })
            ->filter()
            ->values()
            ->avg();

        $swaEncoded = BurialAssistance::where('encoder', auth()->user()->id)->count();
        $logsAdded = ProcessLog::where('added_by', auth()->user()->id)->count();
        $assignedApplications = BurialASsistance::where(function ($query) {
            $query->where('status', '!=', 'rejected')
                ->where('status', '!=', 'released')
                ->where('assigned_to', auth()->user()->id);   
        })->get();

        $cardData = [
            [
                'label' => 'SWAs Encoded',
                'bg' => 'bg-primary',
                'icon' => 'fa-align-left',
                'count' => $swaEncoded,
            ],
            [
                'label' => 'Updates Added',
                'bg' => 'bg-secondary',
                'icon' => 'fa-list',
                'count' => $logsAdded,
            ],
            [
                'label' => 'Avg. Updates/Hour',
                'bg' => 'bg-success',
                'icon' => 'fa-bell-concierge',
                'count' => $updatesPerHour ? number_format($updatesPerHour, 2) . ' hr' : '0 hr',
            ],
            [
                'label' => 'Assigned Applications',
                'bg' => 'bg-info',
                'icon' => 'fa-equals',
                'count' => $assignedApplications->count(),
            ]
        ];

        return view('admin.dashboard', compact(
            'perBarangay',
            'lastLogs',
            'pendingApplications',
            'monthlyActivity',
            'applicationsByBarangay',
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
       $totalRequestLogs = Activity::where('description', 'like', 'Burial Assistance application %')->count();
       $firstRequest = Activity::where('description', 'like', 'Burial Assistance application %')->oldest()->first();
       $lastRequest = Activity::where('description', 'like', 'Burial Assistance application %')->latest()->first();

       if ($firstRequest && $lastRequest) {
        $months = Carbon::parse($firstRequest->created_at)->diffInMonths(Carbon::parse($lastRequest->created_at)) + 1;
        $avgRequestsPerMonth = $totalRequestLogs / $months;
        } else {
            $avgRequestsPerMonth = 0;
        }

        $totalTrackLogs = Activity::where('description', 'like', 'Burial Assistance tracked by guest')->count();
        $firstTrack = Activity::where('description', 'like', 'Burial Assistance tracked by guest')->oldest()->first();
        $lastTrack = Activity::where('description', 'like', 'Burial Assistance tracked by guest')->latest()->first();

        if ($firstTrack && $lastTrack) {
            $months = Carbon::parse($firstTrack->created_at)->diffInMonths(Carbon::parse($lastTrack->created_at)) + 1;
            $avgTracksPerMonth = $totalTrackLogs / $months;
        } else {
            $avgTracksPerMonth = 0;
        }
    
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

        // TODO: Use Deceased barangay instead after updating deceased model
        $applicationsThisYear = Claimant::select('barangay_id', DB::raw('count(*) as total'))
            ->with('barangay')
            ->whereYear('created_at', now()->year)
            ->groupBy('barangay_id')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->barangay->name ?? 'Unknown',
                    'count' => $item->total,
                ];
            });


        $applicationsThisMonth = Claimant::select('barangay_id', DB::raw('count(*) as total'))
            ->with('barangay')
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->groupBy('barangay_id')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->barangay->name ?? 'Unknown',
                    'count' => $item->total,
                ];
            });

        $applicationsThisWeek = Claimant::select('barangay_id', DB::raw('count(*) as total'))
            ->with('barangay')
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->groupBy('barangay_id')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->barangay->name ?? 'Unknown',
                    'count' => $item->total,
                ];
            });

        $applicationsThisDay = Claimant::select('barangay_id', DB::raw('count(*) as total'))
            ->with('barangay')
            ->where('created_at', now())
            ->groupBy('barangay_id')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->barangay->name ?? 'Unknown',
                    'count' => $item->total,
                ];
            });
        
        
        $applicationsByBarangay = BurialAssistance::with(['claimant.barangay'])
            ->whereYear('created_at', now()->year)
            ->get()
            ->groupBy(fn($item) => $item->claimant->barangay->name);

        $applicationsByAdmin = User::whereHas('encoder')
            ->with('encoder')
            ->get();

        $totalApplications = BurialAssistance::where(function ($query) {
            $query->where('status', '!=', 'rejected');
        })->count();

        $lastLogs = ProcessLog::orderBy('created_at', 'desc')->limit(5)->get();
        $pendingApplications = BurialAssistance::where('status', 'pending')->oldest()->limit(10)->get();

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
                    $diffs[] = $start->diffInHours($end);
                }
                return collect($diffs)->avg();
            })
            ->filter()
            ->values();

        $globalAverageProcessing = $applicationAverages->avg();

        $cardData = [
            [
                'label' => 'Applications/Month',
                'bg' => 'bg-primary',
                'icon' => 'fa-align-left',
                'count' => number_format($avgRequestsPerMonth, 2) . '%',
            ],
            [
                'label' => 'Tracks/Month',
                'bg' => 'bg-secondary',
                'icon' => 'fa-list',
                'count' => number_format($avgTracksPerMonth, 2) . '%',
            ],
            [
                'label' => 'Total Applications',
                'bg' => 'bg-success',
                'icon' => 'fa-bell-concierge',
                'count' => $totalApplications,
            ],
            [
                'label' => 'Avg. Processing Time per Update',
                'bg' => 'bg-warning',
                'icon' => 'fa-clock',
                'count' => $globalAverageProcessing ? $globalAverageProcessing . ' hr' : '0 hr',
            ],
        ];

        return view('superadmin.dashboard', compact(
            'perBarangay',
            'applicationsByBarangay',
            'applicationsThisYear',
            'applicationsThisMonth',
            'applicationsThisWeek',
            'applicationsThisDay',
            'cardData',
            'applicationsByAdmin',
            'lastLogs',
            'pendingApplications',
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