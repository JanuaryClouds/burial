<?php

namespace App\Http\Controllers;

use App\Models\Claimant;
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
use Crypt;
use Exception;
use Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Storage;

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
        $renderColumns = ['tracking_no', 'first_name', 'house_no', 'street', 'barangay_id', 'contact_no'];
        $data = Client::select(
                'id',
                'tracking_no',
                'first_name',
                'middle_name',
                'last_name',
                'house_no',
                'street',
                'barangay_id',
                'contact_no',
            )->get();
        return $dataTable
            ->render('client.index', compact(
                'dataTable',
                'page_title',
                'resource',
                'renderColumns',
                'data'
            ));
    }

    public function view($id) {
        $page_title = 'Client information';
        $resource = 'client';
        $client = Client::find($id);
        $readonly = true;

        if ($client) {
            $genders = Sex::select('id', 'name')->get()->pluck('name', 'id');
            $relationships = Relationship::select('id', 'name')->get()->pluck('name', 'id');
            $civilStatus = CivilStatus::select('id', 'name')->get()->pluck('name', 'id');
            $religions = Religion::select('id', 'name')->get()->pluck('name', 'id');
            $nationalities = Nationality::select('id', 'name')->get()->pluck('name', 'id');
            $educations = Education::select('id', 'name')->get()->pluck('name', 'id');
            $assistances = Assistance::select('id', 'name')->get()->pluck('name', 'id');
            $modes = ModeOfAssistance::select('id', 'name')->get()->pluck('name', 'id');
            $barangays = Barangay::select('id', 'name')->get()->pluck('name', 'id');
            $districts = District::select('id', 'name')->get()->pluck('name', 'id');
            $path = "clients/{$client->tracking_no}";
            $storedFiles = Storage::disk('local')->files($path);
            $files = [];

            foreach ($storedFiles as $storedFile) {
                // TODO: Use API to store images
                $encryptedFile = Storage::disk('local')->get($storedFile);
                $decryptedFile = Crypt::decrypt($encryptedFile);
                $finfo = new \finfo(FILEINFO_MIME_TYPE);
                $mime = $finfo->buffer($decryptedFile);
                $files[] = [
                    'name' => basename($storedFile, '.enc'),
                    'path' => $storedFile,
                    'content' => $decryptedFile,
                    'mime' => $mime,
                ];
            }

            return view('client.view', compact(
                'genders',
                'relationships',
                'civilStatus',
                'religions',
                'nationalities',
                'educations',
                'assistances',
                'modes',
                'barangays', 
                'districts',
                'page_title',
                'resource',
                'client',
                'files',
                'readonly',
            ));
        } else {
            return redirect()->back()->with('alertInfo', 'Client not found!');
        }
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
        try {
            $client = $this->clientServices->storeClient($request->validated());
            
            foreach ($request->file('images', []) as $fieldName => $uploadedFile) {
                $extension = $uploadedFile->getClientOriginalExtension();
                $filename = $fieldName . '.' . $extension . '.enc';
                $path = "clients/{$client->tracking_no}/";
                Storage::disk('local')->put($path . $filename, Crypt::encrypt(file_get_contents($uploadedFile)));
            }

            $ip = request()->ip();
            $browser = request()->header('User-Agent');
            activity()
                ->withProperties(['ip' => $ip, 'browser' => $browser])
                ->log('Added the client details: '. $client?->id);
    
            return redirect()
                ->route('landing.page')
                ->with('alertSuccess', 'Client information added successfully!');
        } catch (Exception $e) {
            return redirect()->back()->with('alertInfo', $e->getMessage());
        }
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

    public function assessment(Request $request, $id)
    {
        try {
            $request->validate([
                'problem_presented' => 'required|string|max:255',
                'assessment' => 'required|string|max:255',
            ]);

            $client = Client::find($id);

            if ($client) {
                $client->assessment()->create([
                    'id' => Str::uuid(),
                    'client_id' => $client->id,
                    'problem_presented' => $request['problem_presented'],
                    'assessment' => $request['assessment']
                ]);

                return redirect()->back()->with('alertSuccess', 'Assessment submitted successfully!');
            } else {
                return redirect()->back()->with('alertInfo', 'Client not found.');
            }
        } catch (Exception $e) {
            return redirect()->back()->with('alertInfo', $e->getMessage());
        }
    }
}