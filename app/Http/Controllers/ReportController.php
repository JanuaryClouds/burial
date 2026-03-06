<?php

namespace App\Http\Controllers;

use App\Models\BurialAssistance;
use App\Models\Cheque;
use App\Models\Claimant;
use App\Models\Client;
use App\Models\Deceased;
use App\Models\FuneralAssistance;
use App\Services\ClientService;
use App\Services\BurialAssistanceService;
use App\Services\FuneralAssistanceService;
use App\Services\DatatableService;
use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected $reportServices;
    protected $clientServices;
    protected $burialAssistanceServices;
    protected $funeralAssistanceServices;
    protected $datatableServices;

    public function __construct(
        ReportService $reportService, 
        DatatableService $datatableService,
        ClientService $clientService,
        BurialAssistanceService $burialAssistanceService,
        FuneralAssistanceService $funeralAssistanceService
    ) {
        $this->reportServices = $reportService;
        $this->datatableServices = $datatableService;
        $this->clientServices = $clientService;
        $this->burialAssistanceServices = $burialAssistanceService;
        $this->funeralAssistanceServices = $funeralAssistanceService;
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

        $data = $this->clientServices->reportIndex($startDate, $endDate);
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

        $data = $this->burialAssistanceServices->reportIndex($startDate, $endDate);

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
                'label' => 'Released',
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

    public function deceased(Request $request)
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

        $data = Deceased::with([
            'burialAssistance.claimant.client',
            'religion',
        ])
            ->whereBetween('date_of_death', [$startDate, $endDate])
            ->get()
            ->map(function ($deceased) {
                return [
                    'tracking_no' => $deceased->burialAssistance?->claimant?->client?->tracking_no,
                    'name' => $deceased->fullname(),
                    'address' => $deceased->address . ', ' . $deceased->barangay?->name,
                    'date_of_birth' => $deceased->date_of_birth,
                    'date_of_death' => $deceased->date_of_death,
                    'religion' => $deceased->religion?->name
                ];
            });

        if (request()->expectsJson()) {
            return response()->json([
                'data' => $data->values(),
            ]);
        }

        $columns = $this->datatableServices->getColumns($data, []);

        $deceasedThisMonth = $this->reportServices->deceasedPerMonth($startDate, $endDate);
        $deceasedPerBarangay = $this->reportServices->deceasedPerBarangay($startDate, $endDate);
        $deceasedPerReligion = $this->reportServices->deceasedPerReligion($startDate, $endDate);
        $deceasedPerGender = $this->reportServices->deceasedPerGender($startDate, $endDate);

        return view('reports.index', compact(
            'data',
            'columns',
            'model',
            'deceasedThisMonth',
            'deceasedPerBarangay',
            'deceasedPerReligion',
            'deceasedPerGender',
            'startDate',
            'endDate'
        ));
    }

    public function claimants(Request $request)
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

        $data = Claimant::with([
            'relationship',
            'barangay',
        ])->select('first_name', 'middle_name', 'last_name', 'suffix', 'relationship_to_deceased', 'mobile_number', 'address', 'barangay_id')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->map(function ($claimant) {
                return [
                    'full_name' => $claimant->fullname(),
                    'mobile_number' => $claimant->mobile_number,
                    'address' => $claimant->address . ', ' . $claimant->barangay?->name,
                    'relationship_to_deceased' => $claimant->relationship?->name
                ];
            });

        $columns = $this->datatableServices->getColumns($data, []);

        if (request()->expectsJson()) {
            return response()->json([
                'data' => $data->values(),
            ]);
        }

        $claimantsPerBarangay = $this->reportServices->claimantPerBarangay($startDate, $endDate);
        $claimantsPerRelationship = $this->reportServices->claimantPerRelationship($startDate, $endDate);

        return view('reports.index', compact(
            'data',
            'columns',
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

    public function cheques(Request $request)
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

        $data = Cheque::with([
            'burialAssistance.claimant.client',
        ])
        ->select(
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
            ->get()
            ->map(function ($cheque) {
                return [
                    'tracking_no' => $cheque->burialAssistance?->claimant?->client?->tracking_no,
                    'claimant' => $cheque->burialAssistance?->claimant?->fullname(),
                    'obr_number' => $cheque->obr_number,
                    'cheque_number' => $cheque->cheque_number,
                    'dv_number' => $cheque->dv_number,
                    'amount' => $cheque->amount,
                    'date_issued' => $cheque->date_issued,
                    'date_claimed' => $cheque->date_claimed,
                    'status' => $cheque->status,
                    'created_at' => $cheque->created_at
                ];
            });

        if (request()->expectsJson()) {
            return response()->json([
                'data' => $data->values(),
            ]);
        }

        $columns = $this->datatableServices->getColumns($data, []);
        $chequesPerStatus = $this->reportServices->chequesPerStatus($startDate, $endDate);

        return view('reports.index', compact(
            'data',
            'columns',
            'model',
            'chequesPerStatus',
            'startDate',
            'endDate',
        ));
    }

    public function funerals(Request $request)
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

        $data = $this->funeralAssistanceServices->reportIndex($startDate, $endDate);

        if (request()->expectsJson()) {
            return response()->json([
                'data' => $data->values(),
            ]);
        }

        $columns = $this->datatableServices->getColumns($data, []);
        $funeralsPerStatus = $this->reportServices->funeralsPerStatus($startDate, $endDate);

        return view('reports.index', compact(
            'data',
            'columns',
            'model',
            'funeralsPerStatus',
            'startDate',
            'endDate',
        ));
    }
}
