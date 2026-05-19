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

        if (auth()->user()->roles()->exists()) {
            $cardData = [
                [
                    'model' => 'App\Models\Beneficiary',
                    'label' => 'Total Beneficiaries',
                    'scope' => 'Total',
                    'iconName' => 'people',
                    'iconPathsCount' => 5,
                    'route' => route('beneficiary.index'),
                ],
                [
                    'model' => 'App\Models\Beneficiary',
                    'label' => 'Referred',
                    'scope' => 'Referral',
                    'iconName' => 'route',
                    'iconPathsCount' => 4,
                    'route' => route('referral.index'),
                ],
                [
                    'model' => 'App\Models\Beneficiary',
                    'label' => 'With Burial Assistances',
                    'scope' => 'BurialAssistance',
                    'iconName' => 'file-up',
                    'iconPathsCount' => 2,
                    'route' => route('burial.index'),
                ],
                [
                    'model' => 'App\Models\Beneficiary',
                    'label' => 'With Libreng Libing',
                    'scope' => 'FuneralAssistance',
                    'iconName' => 'file-up',
                    'iconPathsCount' => 2,
                    'route' => route('funeral.index'),
                ],
            ];
        }

        return view('beneficiary.index', [
            'data' => $data,
            'columns' => $columns,
            'cardData' => $cardData ?? null,
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
