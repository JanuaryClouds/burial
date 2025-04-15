<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\District;
use App\Models\Barangay;
use App\Models\ModeOfAssistance;
use App\Models\Sex;
use App\Models\Religion;
use App\Models\Nationality;
use App\Models\CivilStatus;
use App\Models\Relationship;
use App\Models\Education;
use App\Models\Assistance;
use App\Services\ClientService;
use App\DataTables\CmsDataTable;
use App\Http\Requests\ClientRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class ClientController extends Controller
{
    protected $clientServices;

    public function __construct(ClientService $clientService)
    {
        $this->clientServices = $clientService;
    }

    public function index(CmsDataTable $dataTable)
    {
        $page_title = 'Client';
        $resource = 'client';
        $columns = ['Tracking no', 'Full name', 'Address', 'Date of birth', 'Contact number', 'Action'];
        $data = Client::getAllClients();

        return $dataTable
            ->render('client.index', compact(
                'dataTable',
                'page_title',
                'resource',
                'columns',
                'data'
            ));
    }

    public function create()
    {
        $page_title = 'Client information';
        $resource = 'client';
        $sexes = Sex::getAllSexes();
        $religions = Religion::getAllReligions();
        $nationalities = Nationality::getAllNationalities();
        $civils = CivilStatus::getAllCivilStatuses();  
        $relationships = Relationship::getAllRelationships();
        $educations = Education::getAllEducations();
        $districts = District::getAllDistricts();
        $barangays = Barangay::getAllBarangays();
        $assistances = Assistance::getAllAssistances();
        $moas = ModeOfAssistance::getAllMoas();
        $burial = Assistance::getBurial();

        $oldFamilyRows = collect(old('fam_name', []))->map(function ($_, $i) {
            return [
                'name' => old("fam_name.$i"),
                'sex_id' => old("fam_sex_id.$i"),
                'age' => old("fam_age.$i"),
                'civil_id' => old("fam_civil_id.$i"),
                'relationship_id' => old("fam_relationship_id.$i"),
                'occupation' => old("fam_occupation.$i"),
                'income' => old("fam_income.$i"),
            ];
        })->values();
        
        if ($oldFamilyRows->isEmpty()) {
            $oldFamilyRows = collect([[
                'name' => '',
                'sex_id' => '',
                'age' => '',
                'civil_id' => '',
                'relationship_id' => '',
                'occupation' => '',
                'income' => ''
            ]]);
        }

        $oldAssessmentRows = collect(old('ass_problem_presented', []))->map(function ($_, $i) {
            return [
                'problem_presented' => old("ass_problem_presented.$i"),
                'assessment' => old("ass_assessment.$i"),
            ];
        })->values();
        
        if ($oldAssessmentRows->isEmpty()) {
            $oldAssessmentRows = collect([[
                'problem_presented' => '',
                'assessment' => '',
            ]]);
        }

        return view('client.create', compact(
            'page_title',
            'resource',
            'sexes',
            'religions',
            'nationalities',
            'civils', 
            'relationships',
            'educations',
            'districts',
            'barangays',
            'assistances',
            'moas',
            'burial',
            'oldFamilyRows',
            'oldAssessmentRows'
        ));
    }

    public function getLatestTracking()
    {
        $year = Carbon::now()->format('Y');
    
        $latest = Client::where('tracking_no', 'like', "{$year}_%")
            ->latest('created_at')
            ->first();
    
        if ($latest && preg_match('/^' . $year . '_(\d{4})$/', $latest->tracking_no, $matches)) {
            $number = (int) $matches[1] + 1;
            $tracking_no = $year . '_' . str_pad($number, 4, '0', STR_PAD_LEFT);
        } else {
            $tracking_no = $year . '_0001';
        }
    
        return response()->json(['tracking_no' => $tracking_no]);
    }
    
    public function store(ClientRequest $request)
    {
        $client = $this->clientServices->storeClient($request->validated());

        activity()
            ->performedOn($client)
            ->causedBy(Auth::user())
            ->log('Added the client details: ', $client->id);

        return redirect()
            ->route(Auth::user()->getRoleNames()->first() . '.client.index')
            ->with('success', 'Client information added successfully!');
    }

    public function edit(Client $client)
    {
        $page_title = 'Client update';
        $resource = 'client';
        $data = Client::getClientInfo($client);
        $sexes = Sex::getAllSexes();
        $districts = District::getAllDistricts();
        $religions = Religion::getAllReligions();
        $nationalities = Nationality::getAllNationalities();
        $civils = CivilStatus::getAllCivilStatuses();  
        $relationships = Relationship::getAllRelationships();
        $educations = Education::getAllEducations();

        return view('client.edit', compact(
            'page_title',
            'resource',
            'data',
            'sexes',
            'religions',
            'nationalities',
            'civils', 
            'relationships',
            'educations',
            'districts',
        ));
    }
    
    public function update(ClientRequest $request, Client $client)
    {
        $client = $this->clientServices->updateClient($request->validated(), $client);

        activity()
            ->performedOn($client)
            ->causedBy(Auth::user())
            ->log('Updated the client details: ', $client->id);

        return redirect()
            ->route(Auth::user()->getRoleNames()->first() . '.client.edit', $client->id)
            ->with('success', 'Client information updated successfully!');
    }
    
    public function destroy(Client $client)
    {
        $client = $this->clientServices->deleteClient($client);

        activity()
            ->performedOn($client)
            ->causedBy(Auth::user())
            ->log('Deleted a client details: ', $client->id);
        
        return redirect()
            ->route(Auth::user()->getRoleNames()->first() . '.client.index')
            ->with('success', 'Client information deleted successfully!');
    }
}