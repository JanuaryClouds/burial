<?php

namespace App\Http\Controllers;

use App\Models\Barangay;
use App\Models\Deceased;
use App\Models\Religion;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class DeceasedController extends Controller
{
    public function generatePdfReport(Request $request, $startDate, $endDate)
    {
        try {
            $deceased = Deceased::select(
                'id',
                'burial_assistance_id',
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
                ->with(['gender', 'religion', 'barangay', 'burialAssistance'])
                ->whereBetween('date_of_death', [$startDate, $endDate])
                ->get();

            $deceasedPerBarangay = Barangay::query()
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

            $deceasedPerReligion = Religion::query()
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
                ->mapWithKeys(function ($religion) {
                    return [$religion->name => $religion->deceased_count];
                })
                ->toArray();

            $charts = $request->input('charts', []);

            $pdf = Pdf::loadView('pdf.deceased', compact(
                'deceased',
                'deceasedPerBarangay',
                'deceasedPerReligion',
                'startDate',
                'endDate',
                'charts',
            ))
                ->setPaper('letter', 'portrait');

            return $pdf->stream("deceased-{$startDate}-to-{$endDate}.pdf");
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred: '.$e->getMessage());
        }
    }
}
