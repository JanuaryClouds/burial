<?php

namespace App\Http\Controllers;

use App\Http\Requests\BurialServiceRequest;
use Illuminate\Http\Request;
use App\Models\BurialAssistanceRequest;
use App\Models\BurialServiceProvider;
use App\Models\BurialService;
use App\Models\User;
use Illuminate\Support\Facades\DB;

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
        return view('admin.dashboard', compact('serviceRequests', 'providers', 'services', 'requestsData', 'providersData', 'servicesData'));
    }

    public function user()
    {
        return view('user.dashboard');
    }

    public function superadmin()
    {
       $totalData = BurialAssistanceRequest::all()->count() + BurialServiceProvider::all()->count() + BurialService::all()->count();
        
       return view('superadmin.dashboard', compact(
        'totalData'
       ));
    }
}