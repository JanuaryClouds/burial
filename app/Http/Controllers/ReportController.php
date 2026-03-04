<?php

namespace App\Http\Controllers;

use App\Models\BurialAssistance;
use App\Models\Cheque;
use App\Models\Claimant;
use App\Models\Client;
use App\Models\Deceased;
use App\Models\FuneralAssistance;
use App\Services\DatatableService;
use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected $reportServices;
    protected $datatableServices;

    public function __construct(ReportService $reportService, DatatableService $datatableService)
    {
        $this->reportServices = $reportService;
        $this->datatableServices = $datatableService;
    }

    private $reportTypes = [
        'burial_assistance' => 'Burial Assistance Report',
        'deceased' => 'Deceased Persons Report',
        'cheques' => 'Cheques Report',
    ];

    public function clients(Request $request)
    {
        $model = 'clients';
        if ($request->has('start_date') && $request->start_date != '') {
            $startDate = Carbon::parse($request->start_date);
        } else {
            $startDate = Carbon::now()->startOfYear();
        }

        if ($request->has('end_date') && $request->end_date != '') {
            $endDate = Carbon::parse($request->end_date);
        } else {
            $endDate = Carbon::now()->endOfYear();
        }

        $data = Client::with(['user'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('tracking_no', 'asc')
            ->get()
            ->map(function ($client) {
                return [
                    'tracking_no' => $client->tracking_no,
                    'name' => $client->fullname(),
                    'address' => $client->address(),
                    'created_at' => $client->created_at->format('M d, Y'),
                ];
            });

        $columns = $this->datatableServices->getColumns($data, []);

        $clientsPerBarangay = $this->reportServices->clientsPerBarangay($startDate, $endDate);
        $clientsPerAssistance = $this->reportServices->clientsPerAssistance($startDate, $endDate);

        if (request()->expectsJson()) {
            return response()->json([
                'data' => $data->values(), 
            ]);
        }

        return view('reports.index', compact(
            'data',
            'columns',
            'model',
            'clientsPerBarangay',
            'clientsPerAssistance',
            'startDate',
            'endDate'
        ));
    }

    public function burialAssistances(Request $request)
    {
        $model = 'burial-assistances';
        if ($request->has('start_date') && $request->start_date != '') {
            $startDate = Carbon::parse($request->start_date);
        } else {
            $startDate = Carbon::now()->startOfYear();
        }

        if ($request->has('end_date') && $request->end_date != '') {
            $endDate = Carbon::parse($request->end_date);
        } else {
            $endDate = Carbon::now()->endOfYear();
        }

        $data = BurialAssistance::with([
            'claimant',
            'claimant.client',
            'deceased'
        ])
            ->orderBy('application_date', 'asc')
            ->whereBetween('application_date', [$startDate, $endDate])
            ->get()
            ->map(function ($burialAssistance) {
                return [
                    'tracking_no' => $burialAssistance->claimant?->client?->tracking_no,
                    'beneficiary' => $burialAssistance->deceased?->fullname(),
                    'address' => $burialAssistance->claimant?->client?->address(),
                    'applied_at' => $burialAssistance->application_date,
                    'funeraria' => $burialAssistance->funeraria,
                    'amount' => $burialAssistance->amount,
                    'status' => $burialAssistance->status
                ];
            });

        if (request()->expectsJson()) {
            return response()->json([
                'data' => $data->values(),
            ]);
        }

        $columns = $this->datatableServices->getColumns($data, ['status']);

        $deceasedPerBarangay = $this->reportServices->deceasedPerBarangay($startDate, $endDate);
        $deceasedPerReligion = $this->reportServices->deceasedPerReligion($startDate, $endDate);
        $statistics = [
            $burialAssistanceStatistics = [
                'type' => 'burial_assistance',
                'label' => 'Total Applications',
                'color' => 'primary',
                'icon' => 'file',
                'numbers' => [
                    'total' => $data->count(),
                    'pending' => $data->where('status', 'pending')->count(),
                    'processing' => $data->where('status', 'processing')->count(),
                    'approved' => $data->where('status', 'approved')->count(),
                    'released' => $data->where('status', 'released')->count(),
                    'rejected' => $data->where('status', 'rejected')->count(),
                ],
            ],
        ];

        $cardData = [
            [
                'label' => 'Pending',
                'icon' => 'ki-file',
                'pathsCount' => 2,
                'count' => $data->where('status', 'pending')->count(),
            ],
            [
                'label' => 'Processing',
                'icon' => 'ki-file-right',
                'pathsCount' => 2,
                'count' => $data->where('status', 'processing')->count(),
            ],
            [
                'label' => 'Approved',
                'icon' => 'ki-file-added',
                'pathsCount' => 2,
                'count' => $data->where('status', 'approved')->count(),
            ],
            [
                'label' => 'Approved',
                'icon' => 'ki-folder-added',
                'pathsCount' => 2,
                'count' => $data->where('status', 'released')->count(),
            ],
            [
                'label' => 'Rejected',
                'icon' => 'ki-delete-folder',
                'pathsCount' => 2,
                'count' => $data->where('status', 'rejected')->count(),
            ],
        ];

        return view('reports.index', compact(
            'data',
            'columns',
            'model',
            'statistics',
            'deceasedPerBarangay',
            'deceasedPerReligion',
            'startDate',
            'endDate',
            'cardData',
        ));
    }

    public function deceased(Request $request, ReportService $reportService)
    {
        $model = 'deceased';
        if ($request->has('start_date') && $request->start_date != '') {
            $startDate = Carbon::parse($request->start_date);
        } else {
            $startDate = Carbon::now()->startOfYear();
        }

        if ($request->has('end_date') && $request->end_date != '') {
            $endDate = Carbon::parse($request->end_date);
        } else {
            $endDate = Carbon::now()->endOfYear();
        }

        $deceased = Deceased::select('id', 'first_name', 'middle_name', 'last_name', 'suffix', 'gender', 'date_of_birth', 'date_of_death', 'address', 'barangay_id')
            ->whereBetween('date_of_death', [$startDate, $endDate])
            ->get();

        $deceasedThisMonth = $reportService->deceasedPerMonth($startDate, $endDate);
        $deceasedPerBarangay = $reportService->deceasedPerBarangay($startDate, $endDate);
        $deceasedPerReligion = $reportService->deceasedPerReligion($startDate, $endDate);
        $deceasedPerGender = $reportService->deceasedPerGender($startDate, $endDate);

        return view('reports.index', compact(
            'deceased',
            'model',
            'deceasedThisMonth',
            'deceasedPerBarangay',
            'deceasedPerReligion',
            'deceasedPerGender',
            'startDate',
            'endDate'
        ));
    }

    public function claimants(Request $request, ReportService $reportService)
    {
        $model = 'claimants';
        if ($request->has('start_date') && $request->start_date != '') {
            $startDate = Carbon::parse($request->start_date);
        } else {
            $startDate = Carbon::now()->startOfYear();
        }

        if ($request->has('end_date') && $request->end_date != '') {
            $endDate = Carbon::parse($request->end_date);
        } else {
            $endDate = Carbon::now()->endOfYear();
        }

        $claimants = Claimant::select('first_name', 'middle_name', 'last_name', 'suffix', 'relationship_to_deceased', 'mobile_number', 'address', 'barangay_id')->get();
        $claimantsPerBarangay = $reportService->claimantPerBarangay($startDate, $endDate);
        $claimantsPerRelationship = $reportService->claimantPerRelationship($startDate, $endDate);

        return view('reports.index', compact(
            'claimants',
            'model',
            'claimantsPerBarangay',
            'claimantsPerRelationship',
            'startDate',
            'endDate',
        ));
    }

    public function generate(Request $request, ReportService $reportService)
    {
        try {
            $request->validate([
                'report_type' => 'required|string',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);
            $reportType = $request->input('report_type');
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            if ($reportType == 'burial_assistance') {
                return $reportService->burialAssistanceReport($startDate, $endDate);
            } elseif ($reportType == 'deceased') {
                // Implement deceased report generation
                return $reportService->deceasedReport($startDate, $endDate);
            } else {
                return back()->with('error', 'Invalid report type selected.');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while generating the report: '.$e->getMessage());
        }
    }

    public function cheques(Request $request, ReportService $reportService)
    {
        $model = 'cheques';
        if ($request->has('start_date') && $request->start_date != '') {
            $startDate = Carbon::parse($request->start_date);
        } else {
            $startDate = Carbon::now()->startOfYear();
        }

        if ($request->has('end_date') && $request->end_date != '') {
            $endDate = Carbon::parse($request->end_date);
        } else {
            $endDate = Carbon::now()->endOfYear();
        }

        $cheques = Cheque::select(
            'id',
            'burial_assistance_id',
            'claimant_id',
            'obr_number',
            'cheque_number',
            'dv_number',
            'amount',
            'date_issued',
            'date_claimed',
            'status',
            'created_at'
        )
            ->whereBetween('date_issued', [$startDate, $endDate])
            ->get();
        $chequesPerStatus = $reportService->chequesPerStatus($startDate, $endDate);

        return view('reports.index', compact(
            'cheques',
            'model',
            'chequesPerStatus',
            'startDate',
            'endDate',
        ));
    }

    public function funerals(Request $request, ReportService $reportService)
    {
        $model = 'funerals';
        if ($request->has('start_date') && $request->start_date != '') {
            $startDate = Carbon::parse($request->start_date);
        } else {
            $startDate = Carbon::now()->startOfYear();
        }

        if ($request->has('end_date') && $request->end_date != '') {
            $endDate = Carbon::parse($request->end_date);
        } else {
            $endDate = Carbon::now()->endOfYear();
        }

        $funerals = FuneralAssistance::select(
            'id',
            'client_id',
            'approved_at',
            'forwarded_at',
        )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();
        $funeralsPerStatus = $reportService->funeralsPerStatus($startDate, $endDate);

        return view('reports.index', compact(
            'funerals',
            'model',
            'funeralsPerStatus',
            'startDate',
            'endDate',
        ));
    }
}
