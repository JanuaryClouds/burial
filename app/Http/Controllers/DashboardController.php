<?php

namespace App\Http\Controllers;

use App\Services\ApplicationService;
use App\Services\ClientService;
use App\Services\DatatableService;

class DashboardController extends Controller
{
    public function __construct(
        protected ClientService $clientServices,
        protected ApplicationService $applicationServices,
        protected DatatableService $datatableServices
    ) {}

    public function index()
    {
        if (auth()->user()->roles()->count() == 0) {
            return $this->user();
        }

        if (auth()->user()->roles()->count() > 0) {
            return $this->staff();
        }
    }

    public function staff()
    {
        $data = $this->applicationServices->index('tracking_no', 'desc');
        
        if (request()->expectsJson()) {
            return response()->json([
                'data' => $data ? $data->values() : [],
            ]);
        }

        return view('dashboard', [
            'page_title' => 'Dashboard',
            'data' => $data,
            'columns' => $this->datatableServices->getColumns($data)
        ]);
    }

    public function user()
    {
        return view('dashboard', [
            'page_title' => 'Dashboard',
        ]);
    }
}
