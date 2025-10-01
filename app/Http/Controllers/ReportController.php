<?php

namespace App\Http\Controllers;

use App\Models\Claimant;
use App\Models\Deceased;
use Illuminate\Http\Request;
use App\Services\ReportService;
use App\Models\BurialAssistance;
use Carbon\Carbon;

class ReportController extends Controller
{
    protected $reportService;
    
    private $reportTypes = [
        'burial_assistance' => 'Burial Assistance Report',
        'deceased' => 'Deceased Persons Report',
        // TODO: Cheques Report
    ];

    public function burialAssistances() {
        $burialAssistances = BurialAssistance::select('id', 'tracking_no', 'claimant_id', 'deceased_id', 'application_date', 'status', 'created_at')->get();
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

    public function deceased(ReportService $reportService) {
        $deceased = Deceased::select('id', 'first_name', 'middle_name', 'last_name', 'suffix', 'gender', 'date_of_birth', 'date_of_death', 'address', 'barangay_id')->get();
        $deceasedThisMonth = $reportService->deceasedByMonth(Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth());
        $deceasedThisWeek = $reportService->deceasedByWeek(Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek());
        $deceasedThisDay = $reportService->deceasedByDay(Carbon::now()->startOfDay(), Carbon::now()->endOfDay());
        $deceasedByBarangay = Deceased::selectRaw('barangay_id, COUNT(*) as total')
            ->with('barangay')
            ->groupBy('barangay_id')
            ->get()
            ->map(function ($item) {
                return [
                    'name'  => $item->barangay->name ?? 'Unknown',
                    'count' => $item->total,
                ];
            });

        return view('reports.deceased', compact(
            'deceased',
            'deceasedThisMonth',
            'deceasedThisWeek',
            'deceasedThisDay',
            'deceasedByBarangay',
        ));
    }

    public function claimants() {
        // TODO: optimise by using query instead of all
        $claimants = Claimant::select('first_name', 'middle_name', 'last_name', 'suffix', 'relationship_to_deceased', 'mobile_number', 'address', 'barangay_id')->get();
        $claimantsPerBarangay = Claimant::selectRaw('barangay_id, COUNT(*) as total')
            ->with('barangay')
            ->groupBy('barangay_id')
            ->get()
            ->map(function ($item) {
                return [
                    'name'  => $item->barangay->name ?? 'Unknown',
                    'count' => $item->total,
                ];
            });

        $claimantsPerRelationship = Claimant::selectRaw('relationship_to_deceased, COUNT(*) as total')
            ->with('relationship')
            ->groupBy('relationship_to_deceased')
            ->get()
            ->map(function ($item) {
                return [
                    'name'  => $item->relationship->name ?? 'Unknown',
                    'count' => $item->total,
                ];
            });

        return view('reports.claimants', compact(
            'claimants', 
            'claimantsPerBarangay',
            'claimantsPerRelationship'
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
}
