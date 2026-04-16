<?php

namespace App\Http\Controllers;

use App\Services\BeneficiaryService;
use App\Services\DatatableService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class BeneficiaryController extends Controller
{
    protected $beneficiaryServices;

    protected $datatableServices;

    public function __construct(
        BeneficiaryService $beneficiaryService,
        DatatableService $datatableService,
    ) {
        $this->beneficiaryServices = $beneficiaryService;
        $this->datatableServices = $datatableService;
    }

    public function index()
    {
        if (auth()->user()->roles()->count() == 0) {
            $user_id = auth()->user()->id;
        }
        $data = $this->beneficiaryServices->index($user_id ?? null);
        $columns = $this->datatableServices->getColumns($data, []);

        if (request()->expectsJson()) {
            return response()->json([
                'data' => $data->values(),
            ]);
        }
        
        return view('beneficiary.index', [
            'data' => $data,
            'columns' => $columns,
            'page_title' => 'Beneficiaries',
        ]);
    }

    public function generatePdfReport(Request $request, $startDate, $endDate)
    {
        try {
            $beneficiaries = $this->beneficiaryServices->reportIndex($startDate, $endDate);
            $charts = $request->input('charts', []);
            $pdf = Pdf::loadView('pdf.beneficiary', compact(
                'beneficiaries',
                'startDate',
                'endDate',
                'charts'
            ))
                ->setPaper('letter', 'portrait');

            return $pdf->stream("beneficiary-report-{$startDate}-{$endDate}.pdf");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to generate report. '.$e->getMessage());
        }
    }
}
