<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateBeneficiaryRequest;
use App\Services\BeneficiaryService;
use App\Services\DatatableService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class BeneficiaryController extends Controller
{
    public function __construct(
        protected BeneficiaryService $beneficiaryServices,
        protected DatatableService $datatableServices
    ) {}

    public function index()
    {
        $page_title = 'Beneficiaries';
        $personalData = $this->beneficiaryServices->index(auth()->user()->id);
        $personalDataColumns = $this->datatableServices->getColumns($personalData, []);

        $allData = [];
        $allDataColumns = [];
        $cardData = [];
        if (auth()->user()->hasRole('staff')) {
            $allData = $this->beneficiaryServices->index();
            $allDataColumns = $this->datatableServices->getColumns($allData, []);
        }

        if (request()->expectsJson()) {
            return response()->json([
                'personalData' => $personalData ? $personalData->values() : [],
                'allData' => $allData ? $allData->values() : [],
            ]);
        }

        if (auth()->user()->hasRole('staff')) {
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

        return view('beneficiary.index', compact(
            'page_title',
            'allData',
            'allDataColumns',
            'cardData',
            'personalData',
            'personalDataColumns'
        ));
    }

    public function show(string $id)
    {
        $beneficiary = $this->beneficiaryServices->show($id);
        $readonly = ! auth()->user()->hasRole('superadmin');

        return view('beneficiary.view', [
            'page_title' => $beneficiary->fullname(),
            'readonly' => $readonly,
            'beneficiary' => $beneficiary,
        ]);
    }

    public function update(string $id, UpdateBeneficiaryRequest $request)
    {
        try {
            $data = $request->validated();
            $this->beneficiaryServices->update($id, $data);

            activity()
                ->withProperties(['ip' => $request->ip(), 'beneficiary' => $id])
                ->causedBy(auth()->user())
                ->log('Updated beneficiary');

            return redirect()->route('beneficiary.show', $id)
                ->with('success', 'Beneficiary updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update beneficiary. '.(app()->hasDebugModeEnabled() ? $e->getMessage() : ''));
        }
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
