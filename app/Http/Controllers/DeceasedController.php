<?php

namespace App\Http\Controllers;

use App\Models\Barangay;
use App\Models\Religion;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\Deceased;
use Carbon\Carbon;

class DeceasedController extends Controller
{
    public function generatePdfReport(Request $request, $startDate, $endDate) {
        try {
            $deceased = Deceased::select(
                'id', 
                'first_name', 
                'middle_name', 
                'last_name', 
                'suffix', 
                'gender', 
                'date_of_birth', 
                'date_of_death', 
                'address', 
                'barangay_id',
                'religion_id',
                'created_at',
            )
            ->with('gender', 'religion', 'barangay')
            ->whereBetween('date_of_death', [$startDate, $endDate])
            ->get();

            $barangays = Barangay::with(['deceased' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('deceased.date_of_death', [$startDate, $endDate]);
            }])
                ->select('id', 'name')
                ->whereHas('deceased', function($query) use ($startDate, $endDate) {
                    $query->whereBetween('deceased.date_of_death', [$startDate, $endDate]);
                })
                ->get();

            $religions = Religion::with(['deceased' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('deceased.date_of_death', [$startDate, $endDate]);
            }])
                ->select('id', 'name')
                ->with('deceased')
                ->whereHas('deceased', function($query) use ($startDate, $endDate) {
                    $query->whereBetween('deceased.date_of_death', [$startDate, $endDate]);
                })
                ->get();
            
            $charts = $request->input('charts', []);

            $pdf = Pdf::loadView('pdf.deceased', compact(
                'deceased',
                'barangays',
                'religions',
                'startDate',
                'endDate',
                'charts',
                ))
                ->setPaper('letter', 'portrait');

            return $pdf->stream("deceased-{$startDate}-to-{$endDate}.pdf");
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
}
