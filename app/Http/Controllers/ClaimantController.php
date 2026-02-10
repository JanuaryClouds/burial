<?php

namespace App\Http\Controllers;

use App\Models\Barangay;
use App\Models\Claimant;
use App\Models\Relationship;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ClaimantController extends Controller
{
    public function generatePdfReport(Request $request, $startDate, $endDate)
    {
        try {
            $claimants = Claimant::select(
                'id',
                'burial_assistance_id',
                'first_name',
                'middle_name',
                'last_name',
                'suffix',
                'relationship_to_deceased',
                'mobile_number',
                'address',
                'barangay_id'
            )
                ->with(['barangay', 'relationship', 'barangay', 'oldClaimantChanges', 'newClaimantChanges', 'burialAssistance'])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();

            $claimantPerBarangay = Barangay::query()
                ->select('id', 'name')
                ->withCount([
                    'deceased as deceased_count' => function ($query) use ($startDate, $endDate) {
                        $query->whereBetween('date_of_death', [$startDate, $endDate]);
                    },
                ])
                ->whereHas('deceased', function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('date_of_death', [$startDate, $endDate]);
                })
                ->get()
                ->mapWithKeys(function ($barangay) {
                    return [$barangay->name => $barangay->deceased_count];
                })
                ->toArray();

            $relationshipsPerClaimant = Relationship::query()
                ->select('id', 'name')
                ->withCount([
                    'claimant as claimant_count' => function ($query) use ($startDate, $endDate) {
                        $query->whereBetween('created_at', [$startDate, $endDate]);
                    },
                ])
                ->whereHas('claimant', function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                })
                ->get()
                ->mapWithKeys(function ($religion) {
                    return [$religion->name => $religion->claimant_count];
                })
                ->toArray();

            $charts = $request->input('charts', []);
            $pdf = Pdf::loadView('pdf.claimant', compact(
                'claimants',
                'claimantPerBarangay',
                'relationshipsPerClaimant',
                'startDate',
                'endDate',
                'charts',
            ));

            return $pdf->stream('claimant-report.pdf');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while generating the report: '.$e->getMessage());
        }
    }
}
