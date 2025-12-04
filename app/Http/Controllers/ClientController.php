<?php

namespace App\Http\Controllers;

use App\DataTables\CmsDataTable;
use App\Http\Requests\ClientRequest;
use App\Models\Assistance;
use App\Models\Barangay;
use App\Models\Citizen;
use App\Models\CivilStatus;
use App\Models\Client;
use App\Models\Interview;
use App\Models\Sex;
use App\Services\CentralClientService;
use App\Services\ClientService;
use Barryvdh\DomPDF\Facade\Pdf;
use Cache;
use Crypt;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Storage;
use Str;

class ClientController extends Controller
{
    protected $clientServices;

    protected $citizenServices;
    
    public function __construct(ClientService $clientService, CentralClientService $citizenService)
    {
        $this->clientServices = $clientService;
        $this->citizenServices = $citizenService;
    }

    public function index(CmsDataTable $dataTable)
    {
        $page_title = 'Clients';
        $resource = 'client';
        $renderColumns = ['tracking_no', 'first_name', 'house_no', 'street', 'barangay_id', 'contact_no'];
        $data = Client::with([
            'barangay',
            'claimant',
            'funeralAssistance',
        ])
            ->select(
                'id',
                'tracking_no',
                'first_name',
                'middle_name',
                'last_name',
                'house_no',
                'street',
                'barangay_id',
                'contact_no',
                'created_at'
            )
            ->orderBy('created_at', 'desc')
            ->get();

        $clientsWithInterview = Client::with('interviews')->whereHas('interviews')->get()->count();
        $clientsWithAssessments = Client::with('assessment')->whereHas('assessment')->get()->count();
        $clientsWithRecommendation = Client::with('recommendation')->whereHas('recommendation')->get()->count();

        $cardData = [
            [
                'label' => 'Total Clients',
                'icon' => 'ki-people',
                'pathsCount' => 5,
                'count' => Client::select('id')->get()->count(),
            ],
            [
                'label' => 'Clients with Interviews',
                'icon' => 'ki-note-2',
                'pathsCount' => 4,
                'count' => $clientsWithInterview,
            ],
            [
                'label' => 'Clients with Assessments',
                'icon' => 'ki-brifecase-tick',
                'pathsCount' => 3,
                'count' => $clientsWithAssessments,
            ],
            [
                'label' => 'Clients With Recommendation',
                'icon' => 'ki-file-added',
                'pathsCount' => 2,
                'count' => $clientsWithRecommendation,
            ],
        ];

        return $dataTable
            ->render('client.index', compact(
                'dataTable',
                'page_title',
                'cardData',
                'resource',
                'renderColumns',
                'data'
            ));
    }

    public function view($id)
    {
        $resource = 'client';
        $client = Client::find($id);
        $page_title = $client->first_name.' '.$client->last_name."'s Application";
        $page_subtitle = $client->tracking_no.' - '.$client->id;
        $readonly = true;

        if ($client) {
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
                'page_title',
                'page_subtitle',
                'resource',
                'client',
                'files',
                'readonly',
            ));
        } else {
            return redirect()->back()->with('error', 'Client not found.');
        }
    }

    // public function create()
    // {
    //     $page_title = 'Client information';
    //     $resource = 'client';
    //     $sexes = Sex::getAllSexes();
    //     $religions = Religion::getAllReligions();
    //     $nationalities = Nationality::getAllNationalities();
    //     $civils = CivilStatus::getAllCivilStatuses();
    //     $relationships = Relationship::getAllRelationships();
    //     $educations = Education::getAllEducations();
    //     $districts = District::getAllDistricts();
    //     $barangays = Barangay::getAllBarangays();
    //     $assistances = Assistance::getAllAssistances();
    //     $moas = ModeOfAssistance::getAllMoas();
    //     $burial = Assistance::getBurial();

    //     $oldFamilyRows = collect(old('fam_name', []))->map(function ($_, $i) {
    //         return [
    //             'name' => old("fam_name.$i"),
    //             'sex_id' => old("fam_sex_id.$i"),
    //             'age' => old("fam_age.$i"),
    //             'civil_id' => old("fam_civil_id.$i"),
    //             'relationship_id' => old("fam_relationship_id.$i"),
    //             'occupation' => old("fam_occupation.$i"),
    //             'income' => old("fam_income.$i"),
    //         ];
    //     })->values();

    //     if ($oldFamilyRows->isEmpty()) {
    //         $oldFamilyRows = collect([[
    //             'name' => '',
    //             'sex_id' => '',
    //             'age' => '',
    //             'civil_id' => '',
    //             'relationship_id' => '',
    //             'occupation' => '',
    //             'income' => ''
    //         ]]);
    //     }

    //     $oldAssessmentRows = collect(old('ass_problem_presented', []))->map(function ($_, $i) {
    //         return [
    //             'problem_presented' => old("ass_problem_presented.$i"),
    //             'assessment' => old("ass_assessment.$i"),
    //         ];
    //     })->values();

    //     if ($oldAssessmentRows->isEmpty()) {
    //         $oldAssessmentRows = collect([[
    //             'problem_presented' => '',
    //             'assessment' => '',
    //         ]]);
    //     }

    //     return view('client.create', compact(
    //         'page_title',
    //         'resource',
    //         'sexes',
    //         'religions',
    //         'nationalities',
    //         'civils',
    //         'relationships',
    //         'educations',
    //         'districts',
    //         'barangays',
    //         'assistances',
    //         'moas',
    //         'burial',
    //         'oldFamilyRows',
    //         'oldAssessmentRows'
    //     ));
    // }

    public function create()
    {
        $citizen = session('citizen');
        $matched = [];

        if ($citizen) {
            // Helper for fuzzy matching
            $findMatch = function ($value, $options, $strict) {
                if (! $value) {
                    return null;
                }
                $normalizedValue = strtolower(preg_replace('/[^a-z0-9]/i', '', $value));
                
                foreach ($options as $id => $name) {
                    $normalizedOption = strtolower(preg_replace('/[^a-z0-9]/i', '', $name));
                    if ($normalizedValue === $normalizedOption) {
                        return $id;
                    }
                    if ($normalizedValue == 'female') return 1; 
                    if ($normalizedValue == 'male') return 2;

                    if ($strict) {
                        // Check for contains if exact match fails (e.g. "Calzada-tipas" vs "Calzada Tipas")
                        if (str_contains($normalizedOption, $normalizedValue) || str_contains($normalizedValue, $normalizedOption)) {
                            return $id;
                        }
                    }
                }

                return null;
            };

            $barangays = Barangay::pluck('name', 'id');
            $genders = Sex::pluck('name', 'id');
            $civilStatus = CivilStatus::pluck('name', 'id');
            
            if (isset($citizen['gender'])) {
                $matched['sex_id'] = $findMatch($citizen['gender'], $genders, true);
            }

            if (isset($citizen['latest_address']['barangay'])) {
                $matched['barangay_id'] = $findMatch($citizen['latest_address']['barangay'], $barangays, true);
            }

            if (isset($citizen['civil_status'])) {
                $matched['civil_id'] = $findMatch($citizen['civil_status'], $civilStatus, false);
            }
        }

        return view('client.create', compact(
            'matched',
        ));
    }

    public function getLatestTracking()
    {
        $year = Carbon::now()->format('Y');

        $latest = Client::where('tracking_no', 'like', "{$year}_%")
            ->latest('created_at')
            ->first();

        if ($latest && preg_match('/^'.$year.'_(\d{4})$/', $latest->tracking_no, $matches)) {
            $number = (int) $matches[1] + 1;
            $tracking_no = $year.'_'.str_pad($number, 4, '0', STR_PAD_LEFT);
        } else {
            $tracking_no = $year.'_0001';
        }

        return response()->json(['tracking_no' => $tracking_no]);
    }

    public function store(ClientRequest $request)
    {
        try {
            $citizenUuid = session('citizen')['user_id'] ?? null;
            $client = $this->clientServices->storeClient($request->validated(), $citizenUuid);

            foreach ($request->file('images', []) as $fieldName => $uploadedFile) {
                $extension = $uploadedFile->getClientOriginalExtension();
                $filename = $fieldName.'.'.$extension.'.enc';
                $path = "clients/{$client->tracking_no}/";
                Storage::disk('local')->put($path.$filename, Crypt::encrypt(file_get_contents($uploadedFile)));
            }

            $ip = request()->ip();
            $browser = request()->header('User-Agent');
            activity()
                ->withProperties(['ip' => $ip, 'browser' => $browser])
                ->log('Added the client details: '.$client?->id);

            return redirect()
                ->route('landing.page')
                ->with('success', 'Client information added successfully!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Client information added failed! ' . $e->getMessage());
        }
    }

    public function edit(Client $client)
    {
        $page_title = 'Client update';
        $resource = 'client';
        $data = Client::getClientInfo($client);

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
            ->log('Updated the client details: '.$client->id);

        return redirect()
            ->route(Auth::user()->getRoleNames()->first().'.client.edit', $client->id)
            ->with('success', 'Client information updated successfully!');
    }

    public function destroy(Client $client)
    {
        $client = $this->clientServices->deleteClient($client);

        activity()
            ->performedOn($client)
            ->causedBy(Auth::user())
            ->log('Deleted a client details: '.$client->id);

        return redirect()
            ->route(Auth::user()->getRoleNames()->first().'.client.index')
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
                    'assessment' => $request['assessment'],
                ]);

                return redirect()->back()->with('success', 'Assessment created successfully.');
            } else {
                return redirect()->back()->with('error', 'Client not found.');
            }
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function recommendedService(Request $request, $id)
    {
        try {
            $client = Client::find($id);
            if ($request['type'] == 'burial') {
                $request->validate([
                    'referral' => 'nullable|string|max:255',
                    'amount' => 'nullable|string|max:255',
                    'moa_id' => 'exists:mode_of_assistances,id',
                    'type' => 'string|required',
                ]);
                $burialAssistance = Assistance::where('name', 'Burial')->first();
                $client->recommendation()->create([
                    'id' => Str::uuid(),
                    'client_id' => $client->id,
                    'assistance_id' => $burialAssistance->id,
                    'referral' => $request['referral'],
                    'amount' => $request['amount'],
                    'type' => $request['type'],
                    'remarks' => $request['remarks'],
                    'moa_id' => $request['moa_id'],
                ]);

                $application = $this->clientServices->transferClient($client->id);
                activity()
                    ->log('Burial Assistance Application created');

                return redirect()->back()->with('success', 'Successfuly created burial assistance application for the client!');
            } elseif ($request['type'] == 'funeral') {
                $request->validate([
                    'referral' => 'nullable|string|max:255',
                    'type' => 'string|required',
                ]);

                $funeralAssistance = Assistance::where('name', 'Burial')->first();
                $client->recommendation()->create([
                    'id' => Str::uuid(),
                    'client_id' => $client->id,
                    'assistance_id' => $funeralAssistance->id,
                    'referral' => $request['referral'],
                    'type' => $request['type'],
                    'remarks' => $request['remarks'],
                ]);

                $application = $this->clientServices->transferClient($client->id);
                activity()
                    ->log('Funeral Assistance Application created');

                return redirect()->back()->with('success', 'Successfuly created funeral assistance application for the client!');
            } else {
                return redirect()->back()->with('error', 'Invalid request.');
            }
        } catch (Exception $e) {
            $client->recommendation()->delete();

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function generateGISForm($id)
    {
        $client = Client::find($id);

        return $this->clientServices->exportGIS($client);
    }

    public function generatePdfReport(Request $request, $startDate, $endDate)
    {
        try {
            $clients = Client::select(
                'id',
                'tracking_no',
                'first_name',
                'middle_name',
                'last_name',
                'suffix',
                'house_no',
                'street',
                'barangay_id',
                'contact_no',
            )
                ->with('beneficiary')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();

            $charts = $request->input('charts', []);

            $pdf = Pdf::loadView('pdf.client', compact(
                'clients',
                'startDate',
                'endDate',
                'charts',
            ))
                ->setPaper('letter', 'portrait');

            return $pdf->stream("client-report-{$startDate}-{$endDate}.pdf");
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function history() {
        // ! This does prevent unregistered users from the TLC Portal from tracking clients
        $records = Citizen::records();
        if (!$records) {
            return redirect()->route('landing.page')->with('error', 'You do not have permission to access this page.');
        }
        $client = Client::where('citizen_id', session('citizen')['user_id'])->latest()->get()->first();
        
        $page_title = session('citizen')['firstname'] . ' ' . session('citizen')['lastname'] . ' | Client History';
        $readonly = true;
        $disabled = true;
        return view('client.history', compact(
            'records',
            'client',
            'page_title',
            'readonly',
            'disabled',
        ));
    }
}
