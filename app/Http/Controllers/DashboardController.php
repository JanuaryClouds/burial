<?php

namespace App\Http\Controllers;

use App\Http\Requests\BurialServiceRequest;
use App\Models\BurialAssistance;
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
    public function index()
    {
        return view('dashboard');
    }

    public function admin()
    {
        $requestsData = BurialAssistanceRequest::select('barangay_id', DB::raw('count(*) as total'))
            ->with('barangay')
            ->groupBy('barangay_id')
            ->where('status','pending')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->barangay->name ?? 'Unknown',
                    'count' => $item->total,
                ];
            });

        $providersData = BurialServiceProvider::select('barangay_id', DB::raw('count(*) as total'))
            ->with('barangay')
            ->groupBy('barangay_id')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->barangay->name ?? 'Unknown',
                    'count' => $item->total,
                ];
            });

        $servicesData = BurialService::select('barangay_id',
        DB::raw('count(*) as total'))
            ->with('barangay')
            ->groupBy('barangay_id')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->barangay->name ?? 'Unknown',
                    'count' => $item->total,
                ];
            });
        
        $serviceRequests = BurialAssistanceRequest::getBurialAssistanceRequests('pending');
        $providers = BurialServiceProvider::all();
        $services = BurialService::all();

        $pendingApplications = BurialAssistance::where('status', 'pending')->get();
        $processingApplications = BurialAssistance::where('status', 'processing')->get();
        $approvedApplications = BurialAssistance::where('status', 'approved')->get();
        $releasedApplications = BurialAssistance::where('status', 'released')->get();

        return view('admin.dashboard', compact('serviceRequests', 'providers', 'services', 'requestsData', 'providersData', 'servicesData', 'pendingApplications', 'processingApplications', 'approvedApplications', 'releasedApplications'));
    }

    public function user()
    {
        return view('user.dashboard');
    }

    public function superadmin()
    {
       $totalData = BurialAssistanceRequest::all()->count() + BurialServiceProvider::all()->count() + BurialService::all()->count();
       $totalUsers = User::all()->count();

       $totalRequestLogs = Activity::where('description', 'like', 'Burial Assistance Request %')->count();
       $firstRequest = Activity::where('description', 'like', 'Burial Assistance Request %')->oldest()->first();
       $lastRequest = Activity::where('description', 'like', 'Burial Assistance Request %')->latest()->first();

       if ($firstRequest && $lastRequest) {
        $months = Carbon::parse($firstRequest->created_at)->diffInMonths(Carbon::parse($lastRequest->created_at)) + 1;
        $avgRequestsPerMonth = $totalRequestLogs / $months;
        } else {
            $avgRequestsPerMonth = 0;
        }

        $totalTrackLogs = Activity::where('description', 'like', 'Burial Assistance Request tracked by guest')->count();
        $firstTrack = Activity::where('description', 'like', 'Burial Assistance Request tracked by guest')->oldest()->first();
        $lastTrack = Activity::where('description', 'like', 'Burial Assistance Request tracked by guest')->latest()->first();

        if ($firstTrack && $lastTrack) {
            $months = Carbon::parse($firstTrack->created_at)->diffInMonths(Carbon::parse($lastTrack->created_at)) + 1;
            $avgTracksPerMonth = $totalTrackLogs / $months;
        } else {
            $avgTracksPerMonth = 0;
        }
        

       return view('superadmin.dashboard', compact(
        'totalData',
        'totalUsers',
        'avgRequestsPerMonth',
        'avgTracksPerMonth',
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