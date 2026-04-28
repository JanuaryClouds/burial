<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientRequest;
use App\Models\Assistance;
use App\Models\Barangay;
use App\Models\CivilStatus;
use App\Models\Client;
use App\Models\DocumentRequirement;
use App\Models\Sex;
use App\Services\CentralClientService;
use App\Services\ClientService;
use App\Services\DatatableService;
use App\Services\ImageService;
use App\Services\NotificationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Str;

class ClientController extends Controller
{
    protected $clientServices;

    protected $citizenServices;

    protected $imageServices;

    protected $datatableServices;

    protected $notificationServices;

    public function __construct(
        ClientService $clientService,
        CentralClientService $citizenService,
        ImageService $imageService,
        DatatableService $datatableService,
        NotificationService $notificationService
    ) {
        $this->clientServices = $clientService;
        $this->citizenServices = $citizenService;
        $this->imageServices = $imageService;
        $this->datatableServices = $datatableService;
        $this->notificationServices = $notificationService;
    }

    public function index()
    {
        $page_title = 'Clients';

        if (auth()->user()->roles()->count() == 0) {
            $data = $this->clientServices->index('tracking_no', 'asc', auth()->user()->id);
            $columns = $this->datatableServices->getColumns($data, ['client']);
        } elseif (auth()->user()->roles()->count() > 0) {
            $data = $this->clientServices->index('tracking_no', 'asc');
            $columns = $this->datatableServices->getColumns($data, []);

            $clientsWithInterview = Client::has('interviews')->count();
            $clientsWithAssessments = Client::has('assessment')->count();
            $clientsWithRecommendation = Client::has('recommendation')->count();

            $cardData = [
                [
                    'label' => 'Total Clients',
                    'icon' => 'ki-people',
                    'pathsCount' => 5,
                    'count' => Client::count(),
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
        }

        if (request()->expectsJson()) {
            return response()->json([
                'data' => $data->values(),
            ]);
        }

        return view('client.index', [
            'page_title' => $page_title,
            'columns' => $columns,
            'data' => $data,
            'cardData' => $cardData ?? null,
        ]);
    }

    public function show(Client $client)
    {
        try {
            $resource = 'client';
            $client = $this->clientServices->get($client->id);
            $page_title = $client->tracking_no;
            $page_subtitle = $client->fullname()."'s Application";
            $readonly = auth()->user()->cannot('manage-content');
            $released = $client?->claimant?->burialAssistance?->status != 'released' || $client?->funeralAssistance?->forwarded_at != null;

            if ($client) {
                return view('client.view', compact(
                    'page_title',
                    'page_subtitle',
                    'resource',
                    'client',
                    'readonly',
                    'released',
                ));
            } else {
                return redirect()->back()->with('error', 'Client not found.');
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Unable to find application.');
        }
    }

    public function create()
    {
        $documents = DocumentRequirement::burial();
        $page_title = 'New Application';
        $citizen = session('citizen');
        $matched = [];

        if ($citizen) {
            $barangays = Barangay::pluck('name', 'id');
            $genders = Sex::pluck('name', 'id');
            $civilStatus = CivilStatus::pluck('name', 'id');

            if (isset($citizen['sex'])) {
                $matched['sex_id'] = $this->clientServices->match($citizen['sex'], $genders, true);
            }

            if (isset($citizen['barangay'])) {
                $matched['barangay_id'] = $this->clientServices->match($citizen['barangay'], $barangays, true);
            }

            if (isset($citizen['civil_status'])) {
                $matched['civil_id'] = $this->clientServices->match($citizen['civil_status'], $civilStatus, false);
            }
        }

        return view('client.create', compact(
            'matched',
            'page_title'
        ));
    }

    public function store(ClientRequest $request)
    {
        try {
            $result = $this->clientServices->storeClient($request->validated(), Auth::user(), $request->file('images', []));
            $client = $result['client'] ?? null;

            if (! $client) {
                return redirect()->back()->with('error', 'Failed to add client information!');
            }

            $ip = request()->ip();
            $browser = request()->header('User-Agent');
            activity()
                ->withProperties(['ip' => $ip, 'browser' => $browser])
                ->log('Added the client details: '.$client?->id.(($result['uploadError'] ?? false) ? ' images failed to upload' : ''));

            return redirect()
                ->route('client.show', $client)
                ->with('success', 'Client information added successfully!'.(($result['uploadError'] ?? false) ? ' However, some images failed to upload.' : '').' Please remember to bring hard copies of the submitted documents during the interview at the CSWDO Office.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to add client information!'.(app()->hasDebugModeEnabled() ? ': '.$e->getMessage() : ''));
        }
    }

    public function edit(Client $client)
    {
        $page_title = 'Client update';
        $resource = 'client';
        $data = Client::getClientInfo($client);

        return view('client.view', compact(
            'page_title',
            'resource',
            'data',
        ));
    }

    public function update(ClientRequest $request, Client $client)
    {
        $client = $this->clientServices->updateClient($request->validated(), $client);
        activity()
            ->withProperties(['ip' => request()->ip(), 'browser' => request()->header('User-Agent')])
            ->causedBy(Auth::user())
            ->log('Updated the client details: '.$client->id);

        return redirect()
            ->route('client.show', $client)
            ->with('success', 'Client information updated successfully!');
    }

    public function destroy(Client $client)
    {
        $client = $this->clientServices->deleteClient($client);

        activity()
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

                $ip = request()->ip();
                $browser = request()->header('User-Agent');
                activity()
                    ->causedBy(auth()->user())
                    ->withProperties(['ip' => $ip, 'browser' => $browser, 'client' => $client->id])
                    ->log('Added an assessment for the client');

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

            if (! $client) {
                return redirect()->back()->with('error', 'Client not found.');
            }

            $ip = request()->ip();
            $browser = request()->header('User-Agent');
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

                $this->notificationServices->send(
                    $client->user->citizen_uuid,
                    'burial_assistance',
                    'Burial Assistance Application',
                    'Your application for funeral assistance has been approved. You will receive a burial assistance after our process is complete. Thank you for your patience.'
                );

                activity()
                    ->causedBy(Auth::user())
                    ->withProperties(['ip' => $ip, 'browser' => $browser, 'client' => $client->id])
                    ->log('Burial Assistance application created for client');

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

                $this->notificationServices->send(
                    $client->user->citizen_uuid,
                    'funeral_assistance',
                    'Funeral Assistance Application',
                    'Your application for funeral assistance has been approved. Your beneficiary will be given a Libreng Libing Service after our process is complete. Thank you for your patience.'
                );

                activity()
                    ->causedBy(Auth::user())
                    ->withProperties(['ip' => $ip, 'browser' => $browser, 'client' => $client->id])
                    ->log('Created a Libreng Libing application for client');

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
            $clients = $this->clientServices->reportIndex($startDate, $endDate);

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

    public function history()
    {
        $records = Client::where('user_id', auth()->user()->id)->get();
        if (! $records || $records->isEmpty()) {
            return redirect()->route('landing.page')->with('error', 'You do not have permission to access this page.');
        }

        $client = $records->first();
        $page_title = $client->fullname().'\'s History';
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
