<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Services\ClientService;
use App\Services\DatatableService;
use Spatie\Activitylog\Models\Activity;

class DashboardController extends Controller
{
    protected $clientServices;

    protected $datatableServices;

    public function __construct(ClientService $clientService, DatatableService $datatableService)
    {
        $this->clientServices = $clientService;
        $this->datatableServices = $datatableService;
    }

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
        $page_title = 'Dashboard';

        $data = $this->clientServices->index('tracking_no', 'asc');
        $columns = $this->datatableServices->getColumns($data, ['id', 'status', 'show_route']);

        $cardData = [
            [
                'model' => 'App\Models\Client',
                'label' => 'Total Clients',
                'scope' => 'Total',
                'iconName' => 'people',
                'iconPathsCount' => 5,
                'route' => route('client.index'),
            ],
            [
                'model' => 'App\Models\Client',
                'label' => 'Referred',
                'scope' => 'Referral',
                'iconName' => 'route',
                'iconPathsCount' => 4,
                'route' => route('referral.index'),
            ],
            [
                'model' => 'App\Models\Client',
                'label' => 'With Burial Assistances',
                'scope' => 'BurialAssistance',
                'iconName' => 'file-up',
                'iconPathsCount' => 2,
                'route' => route('burial.index'),
            ],
            [
                'model' => 'App\Models\Client',
                'label' => 'With Libreng Libing',
                'scope' => 'FuneralAssistance',
                'iconName' => 'file-up',
                'iconPathsCount' => 2,
                'route' => route('funeral.index'),
            ],
        ];

        return view('dashboard', compact(
            'page_title',
            'cardData',
            'data',
            'columns',
        ));
    }

    public function user()
    {
        return view('dashboard', [
            'page_title' => 'Dashboard',
        ]);
    }
}
