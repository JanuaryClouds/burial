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
        $isStaff = Auth::user()->roles()->count() > 0;

        if ($isStaff) {
            return view('dashboard', [
                'pageTitle' => 'Dashboard',
                'isStaff' => $isStaff,
            ]);
        }

        $data = $this->services->index(Auth::id());

        if (request()->expectsJson()) {
            return response()->json([
                'data' => $data ? $data->values() : [],
            ]);
        }

        return view('dashboard', [
            'pageTitle' => 'Dashboard',
            'data' => $data,
            'columns' => $this->datatableServices->getColumns($data),
            'isStaff' => $isStaff,
        ]);
    }
}
