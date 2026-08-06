<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBeneficiaryRequest;
use App\Http\Requests\UpdateBeneficiaryRequest;
use App\Models\Beneficiary;
use App\Services\BeneficiaryFamilyService;
use App\Services\BeneficiaryService;
use App\Services\DatatableService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BeneficiaryController extends Controller
{
    public function __construct(
        protected BeneficiaryService $services,
        protected BeneficiaryFamilyService $familyServices,
        protected DatatableService $datatableServices
    ) {}

    public function index()
    {
        $user = Auth::user();
        $beneficiaries = $user->roles->isNotEmpty() ? $this->services->index() : $this->services->index($user->id);
        $columns = $this->datatableServices->getColumns($beneficiaries);

        if (request()->expectsJson()) {
            return $this->datatableServices->ajax($beneficiaries);
        }

        return view('beneficiary.index', [
            'page_title' => 'Beneficiaries',
            'beneficiaries' => $beneficiaries,
            'columns' => $columns,
        ]);
    }

    public function create()
    {
        if (session()->has('beneficiary_uuid')) {
            session()->remove('beneficiary_uuid');
        }

        return view('beneficiary.create', [
            'page_title' => 'Register a Beneficiary',
        ]);
    }

    public function show(Beneficiary $beneficiary)
    {
        $application = $beneficiary->application;

        return view('beneficiary.show', [
            'page_title' => $beneficiary->fullname().' | Beneficiary | '.($application ? $application->tracking_no : 'Draft'),
            'application' => $application ?? null,
            'client' => $application?->client,
            'beneficiary' => $beneficiary,
            'family' => $beneficiary->family,
        ]);
    }

    public function store(StoreBeneficiaryRequest $request)
    {
        try {
            $beneficiary = $this->services->store($request->validated());
            $this->familyServices->store($request->validated(), $beneficiary);

            session()->put('beneficiary_uuid', $beneficiary->uuid);

            return redirect()
                ->route('application.create')
                ->with('success', 'Successfully recorded beneficiary and their family composition. You may edit it before finalizing your application.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Unable to save beneficiary. '.(app()->hasDebugModeEnabled() ? $e->getMessage() : ''));
        }
    }

    public function edit(Beneficiary $beneficiary)
    {
        return view('beneficiary.edit', [
            'beneficiary' => $beneficiary,
            'page_title' => 'Edit '.$beneficiary->fullname(),
        ]);
    }

    public function update(UpdateBeneficiaryRequest $request, Beneficiary $beneficiary)
    {
        try {
            $data = $request->validated();
            $this->services->update($data, $beneficiary);

            activity()
                ->withProperties(['ip' => request()->ip(), 'browser' => request()->userAgent(), 'beneficiary' => $beneficiary->uuid])
                ->causedBy(Auth::user())
                ->log('Updated beneficiary');

            return redirect()->route('beneficiary.show', $beneficiary)
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
