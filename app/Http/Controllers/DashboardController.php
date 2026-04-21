<?php

namespace App\Http\Controllers;

use App\Models\BurialAssistance;
use App\Models\Client;
use App\Models\FuneralAssistance;
use App\Models\Notification;
use App\Services\ClientService;
use App\Services\DatatableService;
use Carbon\Carbon;
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
        $pendingBurialAssistance = BurialAssistance::where(function ($query) {
            $query
                ->orWhere('status', 'pending')
                ->orWhere('status', 'processing');
        })->count();
        $pendingFuneralAssistance = FuneralAssistance::where(function ($query) {
            $query->where('approved_at', null);
            $query->where('forwarded_at', null);
        })->count();

        $pendingClients = Client::where(function ($query) {
            $query->where('created_at', '>=', Carbon::now()->subDays(7));
        })->count();

        $data = $this->clientServices->index('tracking_no', 'asc');
        $columns = $this->datatableServices->getColumns($data, ['id', 'status', 'show_route']);

        $cardData = [
            [
                'label' => 'Clients this week',
                'icon' => 'ki-chart-line',
                'pathsCount' => 2,
                'link' => route('client.index'),
                'count' => $pendingClients,
            ],
            [
                'label' => 'Pending Burial Assistance',
                'icon' => 'ki-timer',
                'pathsCount' => 2,
                'link' => route('burial.index', ['status' => 'pending']),
                'count' => $pendingBurialAssistance,
            ],
            [
                'label' => 'Pending Libreng Libing Applications',
                'icon' => 'ki-watch',
                'pathsCount' => 2,
                'link' => route('funeral.index'),
                'count' => $pendingFuneralAssistance,
            ],
        ];

        return view('dashboard', compact([
            'cardData',
            'page_title',
            'data',
            'columns',
        ]));
    }

    public function user()
    {
        $page_title = 'Dashboard';
        $latest_record = Client::where('user_id', auth()->user()->id)->orderBy('created_at', 'desc')->first();

        if ($latest_record) {
            $latest_record->load(['interviews', 'claimant', 'funeralAssistance']);
        }

        return view('dashboard', [
            'page_title' => $page_title,
            'latest_record' => $latest_record,
        ]);
    }

    public function trackerEvents()
    {
        $logs = Activity::where('description', 'like', 'Burial Assistance Request tracked by guest')->get();

        $events = $logs->map(function ($log) {
            return [
                'title' => $log->properties,
                'date' => $log->created_at->toDateString(),
            ];
        });

        return response()->json($events);
    }
}
