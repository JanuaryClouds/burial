<?php

namespace App\Http\Controllers;

use App\Models\Cheque;
use App\Models\Claimant;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ChequeController extends Controller
{
    public function generatePdfReport(Request $request, $startDate, $endDate)
    {
        try {
            if (! strtotime($startDate) || ! strtotime($endDate)) {
                return redirect()->back()->with('error', 'Invalid date format.');
            }

            $cheques = Cheque::with('burialAssistance.claimant.client')
                ->whereBetween('date_issued', [$startDate, $endDate])
                ->get()
                ->map(function ($cheque) {
                    return [
                        'tracking_no' => $cheque->burialAssistance?->originalClaimant()?->client?->tracking_no,
                        'check_number' => $cheque->cheque_number ?? 'N/A',
                        'obr_number' => $cheque->obr_number ?? 'N/A',
                        'dv_number' => $cheque->dv_number ?? 'N/A',
                        'amount' => $cheque->amount ?? 0,
                        'date_issued' => $cheque->date_issued ?? 'N/A',
                        'date_claimed' => $cheque->date_claimed ?? 'N/A',
                    ];
                });

            $claimants = Claimant::with([
                'cheque',
                'client',
                'barangay',
                'burialAssistance',
                'burialAssistance.claimantChanges',
                'burialAssistance.claimantChanges.newUserClaimant',
            ])
                ->whereHas('cheque', function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('date_issued', [$startDate, $endDate]);
                })
                ->get()
                ->map(function ($claimant) {
                    $burialAssistance = $claimant->burialAssistance;
                    $claimant = $burialAssistance->currentClaimant();

                    return [
                        'tracking_no' => $burialAssistance->originalClaimant()->client?->tracking_no,
                        'claimant' => $claimant->fullname(),
                        'contact_number' => $claimant->contact_number,
                        'address' => $claimant->fullAddress(),
                        'cheque_number' => $claimant->cheque ? $claimant->cheque->cheque_number : 'N/A',
                    ];
                });

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
            return back()->with('error', 'An error occurred: '.$e->getMessage());
        }
    }
}
