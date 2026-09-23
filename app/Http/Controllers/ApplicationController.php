<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreApplicationRequest;
use App\Http\Requests\UpdateApplicationRequest;
use App\Models\Application;
use App\Models\Beneficiary;
use App\Models\Client;
use App\Models\FuneralAssistanceType;
use App\Models\ModeOfAssistance;
use App\Services\ApplicationService;
use App\Services\BeneficiaryFamilyService;
use App\Services\BeneficiaryService;
use App\Services\ClientService;
use App\Services\DatatableService;
use App\Services\ImageService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{
    public function __construct(
        protected DatatableService $datatableServices,
        protected ApplicationService $services,
        protected ClientService $clientServices,
        protected BeneficiaryService $beneficiaryServices,
        protected BeneficiaryFamilyService $beneficiaryFamilyServices,
        protected ImageService $imageServices,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = $this->services->index(
            Auth::user()->roles->isNotEmpty() ? null : Auth::user()->id, 'tracking_no', 'desc'
        );

        if (request()->expectsJson()) {
            return $this->datatableServices->ajax($data);
        }

        return view('application.index', [
            'columns' => $this->datatableServices->getColumns($data),
            'pageTitle' => 'Applications',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $draftedClients = Auth::user()->clients()->where(function ($query) {
            $query->whereDoesntHave('application');
        })->get();

        $draftedBeneficiaries = Auth::user()->beneficiaries()->where(function ($query) {
            $query->whereDoesntHave('application');
        })->get();

        if ($draftedClients->count() == 0) {
            return redirect()->route('client.create', [
                'pageTitle' => 'Draft a Client Record',
            ]);
        }

        if ($draftedBeneficiaries->count() == 0) {
            return redirect()->route('beneficiary.create');
        }

        return view('application.create', [
            'pageTitle' => 'Create Application',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreApplicationRequest $request)
    {
        try {
            $validated = $request->validated();

            $client = Client::where('uuid', $validated['client_uuid'])
                ->where('user_id', Auth::user()->id)
                ->firstOrFail();

            $beneficiary = Beneficiary::where('uuid', $validated['beneficiary_uuid'])
                ->where('created_by', Auth::user()->id)
                ->firstOrFail();

            $application = $this->services->store($client, $beneficiary);

            // Upload any submitted document images (skipped gracefully when the fileserver is disabled)
            $uploadError = false;
            foreach ($request->file('images', []) as $fieldName => $uploadedFile) {
                try {
                    $this->imageServices->post($application->tracking_no.'-'.$fieldName, $uploadedFile);
                } catch (\Throwable $th) {
                    report($th);
                    $uploadError = true;
                }
            }

            // Clear session UUIDs
            session()->forget(['client_uuid', 'beneficiary_uuid']);

            return redirect()->route('application.index')
                ->with('success', 'Application submitted successfully! Your tracking number is: '.$application->tracking_no
                    .($uploadError ? ' However, some documents failed to upload.' : ''));
        } catch (\Exception $e) {
            activity()
                ->causedBy(Auth::user())
                ->withProperties([
                    'ip' => request()->ip(),
                    'browser' => request()->userAgent(),
                    'error' => $e->getMessage(),
                ])
                ->log('Unable to store application');

            return redirect()->back()
                ->with('error', 'Unable to submit application'.(config('app.debug') ? ': '.$e->getMessage() : ''));
        }
    }

    /**
     * Display the search page to utilize the QR and Barcodes.
     */
    public function search()
    {
        return view('application.search', [
            'pageTitle' => 'Search Application',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Application $application)
    {
        return view('application.show', [
            'application' => $application,
            'pageTitle' => $application->tracking_no,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Application $application)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateApplicationRequest $request, Application $application)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    // public function destroy(Application $application)
    // {
    //     //
    // }

    /**
     * Summary of print
     */
    public function print(Application $application)
    {
        return $this->services->print($application);
    }

    /**
     * Summary of codes
     */
    public function codes(Application $application)
    {
        return $this->services->codes($application);
    }

    /**
     * Summary of certificate
     *
     * @return Response
     */
    public function certificate(Application $application)
    {
        if (! $application->recommendations()->exists()) {
            abort(403);
        }

        return $this->services->certificate($application);
    }
}
