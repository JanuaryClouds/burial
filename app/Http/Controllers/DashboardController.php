<?php

namespace App\Http\Controllers;

use App\Services\ApplicationService;
use App\Services\ClientService;
use App\Services\DataTableService;

class DashboardController extends Controller
{
    public function __construct(
        protected ApplicationService $services,
        protected ClientService $clientServices,
        protected DataTableService $dataTableServices
    ) {}

    public function index()
    {
        return view('dashboard', [
            'pageTitle' => 'Dashboard',
        ]);
    }
}
