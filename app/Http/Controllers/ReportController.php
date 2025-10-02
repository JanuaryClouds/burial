<?php

namespace App\Http\Controllers;

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

    public function deceased(Request $request, ReportService $reportService) {
        if ($request->has('start_date') && $request->start_date != '') {
            $startDate = Carbon::parse($request->start_date);
        } else {
            $startDate = Carbon::now()->subMonth();
        }
        
        if ($request->has('end_date') && $request->end_date != '') {
            $endDate = Carbon::parse($request->end_date);
        } else {
            $endDate = Carbon::now()->addMonth();
        }

        $deceased = Deceased::select('id', 'first_name', 'middle_name', 'last_name', 'suffix', 'gender', 'date_of_birth', 'date_of_death', 'address', 'barangay_id')
            ->whereBetween('date_of_death', [$startDate, $endDate])
            ->get();

        $deceasedThisMonth = $reportService->deceasedByMonth($startDate, $endDate);
        $deceasedByBarangay = $reportService->deceasedByBarangay($startDate, $endDate);
        $deceasedByReligion = $reportService->deceasedByReligion($startDate, $endDate);
        $deceasedByGender = $reportService->deceasedByGender($startDate, $endDate);
        return view('reports.deceased', compact(
            'deceased',
            'deceasedThisMonth',
            'deceasedByBarangay',
            'deceasedByReligion',
            'deceasedByGender',
            'startDate',
            'endDate'
        ));
    }

    public function claimants() {
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
