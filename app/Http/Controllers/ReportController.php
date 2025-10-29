<?php

namespace App\Http\Controllers;

use App\Models\Cheque;
use App\Models\Claimant;
use App\Models\Deceased;
use Illuminate\Http\Request;
use App\Services\ReportService;
use App\Models\BurialAssistance;
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

    public function burialAssistances(Request $request, ReportService $reportService) {
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

        return view('reports.burial-assistances', compact(
            'burialAssistances', 
            'statistics',
            'deceasedPerBarangay',
            'deceasedPerReligion',
            'startDate',
            'endDate',
        ));
    }

    public function deceased(Request $request, ReportService $reportService) {
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
        return view('reports.deceased', compact(
            'deceased',
            'deceasedThisMonth',
            'deceasedPerBarangay',
            'deceasedPerReligion',
            'deceasedPerGender',
            'startDate',
            'endDate'
        ));
    }

    public function claimants(Request $request, ReportService $reportService) {
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

        return view('reports.claimants', compact(
            'claimants', 
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

        return view('reports.cheques', compact(
            'cheques', 
            'chequesPerStatus',
            'startDate',
            'endDate',
        ));
    }
}
