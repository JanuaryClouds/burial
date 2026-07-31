<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Http\Requests\StoreApplicationRequest;
use App\Http\Requests\UpdateApplicationRequest;
use App\Models\Beneficiary;
use App\Models\Client;
use App\Models\FuneralAssistanceType;
use App\Models\ModeOfAssistance;
use App\Models\Relationship;
use App\Services\ApplicationService;
use App\Services\BeneficiaryFamilyService;
use App\Services\ClientService;
use App\Services\BeneficiaryService;
use App\Services\DatatableService;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{
    public function __construct(
        protected DatatableService $datatableServices,
        protected ApplicationService $services,
        protected ClientService $clientServices,
        protected BeneficiaryService $beneficiaryServices,
        protected BeneficiaryFamilyService $beneficiaryFamilyServices,
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
            'page_title' => 'Applications',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        
        if ($user->roles->isNotEmpty()) {
            return redirect()->route('dashboard')
                ->with('warning', 'You are not allowed to apply as a client');
        }

        $draftClients = Client::with(Client::relations())
            ->whereDoesntHave('application')
            ->where('user_id', $user->id)
            ->get();

        $draftBeneficiaries = Beneficiary::with(Beneficiary::relations())
            ->whereDoesntHave('application')
            ->where('created_by', $user->id)
            ->get();

        $clientOptions = $draftClients
            ->mapWithKeys(function (Client $client) {
                return [
                    $client->uuid => $client->fullname() . ' - created at ' . $client->created_at->format('M d, Y : h:m a'),
                ];
            })
            ->toArray();
            
        $beneficiaryOptions = $draftBeneficiaries
            ->mapWithKeys(function (Beneficiary $beneficiary) {
                return [
                    $beneficiary->uuid => $beneficiary->fullname() . ' - created at ' . $beneficiary->created_at->format('M d, Y : h:m a'),
                ];
            })
            ->toArray();

        return view('application.create', [
            'page_title' => 'Create Application',
            'draftClients' => $draftClients,
            'draftBeneficiaries' => $draftBeneficiaries,
            'clientOptions' => $clientOptions,
            'beneficiaryOptions' => $beneficiaryOptions,
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

            // Clear session UUIDs
            session()->forget(['client_uuid', 'beneficiary_uuid']);

            return redirect()->route('application.index')
                ->with('success', 'Application submitted successfully! Your tracking number is: ' . $application->tracking_no);
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
                ->with('error', 'Unable to submit application' . (config('app.debug') ? ': ' . $e->getMessage() : ''));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Application $application)
    {
        $application->loadMissing(Application::relations(
            'client', 
            'beneficiary', 
            'recommendation', 
            'assessment', 
            'processLogs', 
            'referral',
            'relationship',
        ));

        $client = $application->client;
        $beneficiary = $application->beneficiary;
        $family = $beneficiary->family;
        $interviews = $client->interviews;
        $assessment = $application->assessment;
        $recommendations = $application->recommendations;
        $referral = $application->referral;
        $funeralAssistanceTypes = FuneralAssistanceType::select(['name', 'uuid'])->get();
        $modes = ModeOfAssistance::select(['id', 'name'])->get();

        $conditions = $this->services->workflowState($application);

        return view('application.show', [
            'application' => $application,
            'client' => $client,
            'beneficiary' => $beneficiary,
            'family' => $family,
            'interviews' => $interviews,
            'assessment' => $assessment,
            'recommendations' => $recommendations,
            'referral' => $referral,
            'conditions' => $conditions,
            'funeralAssistanceTypes' => $funeralAssistanceTypes,
            'modes' => $modes,
            'page_title' => $application->tracking_no
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
     * @param Application $application
     */
    public function print(Application $application)
    {
        return $this->services->print($application);
    }

    /**
     * Summary of certificate
     * @param Application $application
     * @return \Illuminate\Http\Response
     */
    public function certificate(Application $application)
    {
        return $this->services->certificate($application);
    }
}
