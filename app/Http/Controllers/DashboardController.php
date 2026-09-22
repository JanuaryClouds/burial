<?php

namespace App\Http\Controllers;

use App\Services\ApplicationService;
use App\Services\ClientService;
use App\Services\DatatableService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(
        protected ApplicationService $services,
        protected ClientService $clientServices,
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

        return view('dashboard', [
            'pageTitle' => 'Dashboard',
        ]);
    }

    public function user()
    {
        $data = $this->services->index(Auth::id(), 'tracking_no', 'desc');

        if (request()->expectsJson()) {
            return response()->json([
                'data' => $data ? $data->values() : [],
            ]);
        }

        return view('dashboard', [
            'pageTitle' => 'Dashboard',
            'data' => $data,
            'columns' => $this->datatableServices->getColumns($data),
        ]);
    }
}
