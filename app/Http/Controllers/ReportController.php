<?php

namespace App\Http\Controllers;

use App\Models\Cheque;
use App\Models\Claimant;
use App\Models\Deceased;
use Illuminate\Http\Request;
use App\Services\ReportService;
use App\Models\Client;
use App\Models\BurialAssistance;
use App\Models\FuneralAssistance;
use Carbon\Carbon;
use Storage;

class ReportController extends Controller
{
    protected $reportService;
    
    private $reportTypes = [
        'burial_assistance' => 'Burial Assistance Report',
        'deceased' => 'Deceased Persons Report',
        'cheques' => 'Cheques Report',
    ];

    public function clients(Request $request, ReportService $reportService) {
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

        $clients = Client::select(
                'id', 
                'tracking_no', 
                'first_name', 
                'middle_name', 
                'last_name', 
                'suffix', 
                'contact_no', 
                'house_no', 
                'street', 
                'barangay_id'
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $clientsPerBarangay = $reportService->clientsPerBarangay($startDate, $endDate);
        $clientsPerAssistance = $reportService->clientsPerAssistance($startDate, $endDate);
        return view('reports.index', compact(
            'clients',
            'model',
            'clientsPerBarangay',
            'clientsPerAssistance',
            'startDate',
            'endDate'
        ));
    }

    public function burialAssistances(Request $request, ReportService $reportService) {
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

        $burialAssistances = BurialAssistance::select('id', 'tracking_no', 'claimant_id', 'deceased_id', 'application_date', 'funeraria', 'amount', 'status', 'created_at')
            ->orderBy('tracking_no', 'asc')
            ->whereBetween('application_date', [$startDate, $endDate])
            ->get();
            
        $deceasedPerBarangay = $reportService->deceasedPerBarangay($startDate, $endDate);
        $deceasedPerReligion = $reportService->deceasedPerReligion($startDate, $endDate); 
        $statistics = [
            $burialAssistanceStatistics = [
                'type' => 'burial_assistance',
                'label' => 'Total Applications',
                'color' => 'primary',
                'icon' => 'file',
                'numbers' => [
                    'total' => $burialAssistances->count(),
                    'pending' => $burialAssistances->where('status', 'pending')->count(),
                    'processing' => $burialAssistances->where('status', 'processing')->count(),
                    'approved' => $burialAssistances->where('status', 'approved')->count(),
                    'released' => $burialAssistances->where('status', 'released')->count(),
                    'rejected' => $burialAssistances->where('status', 'rejected')->count(),
                ]
            ],
        ];

        $cardData = [
            [
                'label' => 'Pending',
                'icon' => 'ki-file',
                'pathsCount' => 2,
                'count' => $burialAssistances->where('status', 'pending')->count(),
            ],
            [
                'label' => 'Processing',
                'icon' => 'ki-file-right',
                'pathsCount' => 2,
                'count' => $burialAssistances->where('status', 'processing')->count(),
            ],
            [
                'label' => 'Approved',
                'icon' => 'ki-file-added',
                'pathsCount' => 2,
                'count' => $burialAssistances->where('status', 'approved')->count(),
            ],
            [
                'label' => 'Approved',
                'icon' => 'ki-folder-added',
                'pathsCount' => 2,
                'count' => $burialAssistances->where('status', 'released')->count(),
            ],
            [
                'label' => 'Rejected',
                'icon' => 'ki-delete-folder',
                'pathsCount' => 2,
                'count' => $burialAssistances->where('status', 'rejected')->count(),
            ],
        ];

        return view('reports.index', compact(
            'burialAssistances', 
            'model',
            'statistics',
            'deceasedPerBarangay',
            'deceasedPerReligion',
            'startDate',
            'endDate',
            'cardData',
        ));
    }

    public function deceased(Request $request, ReportService $reportService) {
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

    public function claimants(Request $request, ReportService $reportService) {
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

    public function generate(Request $request, ReportService $reportService) {
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
                return back()->with('alertError', 'Invalid report type selected.');
            }
        } catch (\Exception $e) {
            return back()->with('alertError', 'An error occurred while generating the report: ' . $e->getMessage());
        }
    }

    public function cheques(Request $request, ReportService $reportService) {
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

    public function funerals(Request $request, ReportService $reportService) {
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
