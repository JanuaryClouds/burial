<?php

namespace App\Http\Controllers;

use App\Models\Cheque;
use App\Models\Claimant;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ChequeController extends Controller
{
    public function generatePdfReport(Request $request, $startDate, $endDate) {
        try {
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
                'created_at', 
            )
            ->with('burialAssistance', 'claimant')
            ->whereBetween('date_issued', [$startDate, $endDate])
            ->get();

            $claimants = Claimant::select('id', 'first_name', 'middle_name', 'last_name', 'suffix', 'mobile_number', 'address', 'barangay_id')
                ->with('cheque')
                ->get();
            $charts = $request->input('charts', []);

            $pdf = Pdf::loadView('pdf.cheque', compact(
                'cheques',
                'claimants',
                'startDate',
                'endDate',
                'charts',
                ))
                ->setPaper('letter', 'portrait');

            return $pdf->stream("cheques-{$startDate}-to-{$endDate}.pdf");
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
}
