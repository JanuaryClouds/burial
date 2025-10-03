<?php

namespace App\Http\Controllers;

use App\Models\Barangay;
use App\Models\Claimant;
use App\Models\Relationship;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ClaimantController extends Controller
{
    public function generatePdfReport(Request $request, $startDate, $endDate) {
        try {
            $claimants = Claimant::select(
                'id',
                'first_name',
                'middle_name',
                'last_name',
                'suffix',
                'relationship_to_deceased',
                'mobile_number',
                'address',
                'barangay_id'
            )
            ->with('barangay', 'relationship', 'barangay', 'oldClaimantChanges', 'newClaimantChanges', 'burialAssistance')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();
            
            $barangays = Barangay::with(['claimant' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }])
                ->select('id', 'name')
                ->whereHas('claimant', function($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                })
                ->get();

            $relationships = Relationship::with(['claimant' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }])
                ->select('id', 'name')
                ->whereHas('claimant', function($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                })
                ->get();

            $charts = $request->input('charts', []);
            $pdf = Pdf::loadView('pdf.claimant', compact(
                'claimants', 
                'barangays', 
                'relationships',
                'startDate',
                'endDate',
                'charts',
            ));
            return $pdf->stream('claimant-report.pdf');
        } catch (\Exception $e) {
            return back()->with('alertError', 'An error occurred while generating the report: ' . $e->getMessage());
        }
    }
}
