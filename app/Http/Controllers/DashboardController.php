<?php

namespace App\Http\Controllers;

use App\Http\Requests\BurialServiceRequest;
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
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as count')
        )
        ->groupBy('month')
        ->get()
        ->map(function ($item) {
            return [
                'month' => Carbon::create()->month($item->month)->format('F'),
                'count' => $item->count,
            ];
        });

        return view('admin.dashboard', compact(
            'perBarangay',
            'lastLogs',
            'pendingApplications',
            'monthlyActivity',
            'applicationsByBarangay',
        ));
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