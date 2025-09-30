<?php

namespace App\Http\Controllers;

use App\Models\Claimant;
use App\Models\Deceased;
use Illuminate\Http\Request;
use App\Services\ReportService;
use App\Models\BurialAssistance;

class ReportController extends Controller
{
    protected $reportService;
    
    private $reportTypes = [
        'burial_assistance' => 'Burial Assistance Report',
        'deceased' => 'Deceased Persons Report',
        // TODO: Cheques Report
    ];

    public function burialAssistances() {
        $burialAssistances = BurialAssistance::all();
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
        return view('reports.burial-assistances', compact('burialAssistances', 'statistics'));
    }

    public function deceased() {
        // TODO: optimise by using query instead of all
        $deceased = Deceased::all();
        return view('reports.deceased', compact('deceased'));
    }

    public function claimants() {
        // TODO: optimise by using query instead of all
        $claimants = Claimant::all();
        return view('reports.claimants', compact('claimants'));
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
}
