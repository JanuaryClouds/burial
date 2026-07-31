<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Models\Assistance;
use App\Models\Barangay;
use App\Models\CivilStatus;
use App\Models\Client;
use App\Models\ClientAssessment;
use App\Models\ClientRecommendation;
use App\Models\Sex;
use App\Services\CentralClientService;
use App\Services\ClientService;
use App\Services\DatatableService;
use App\Services\NotificationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ClientController extends Controller
{
    public function __construct(
        protected ClientService $services,
        protected CentralClientService $citizenServices,
        protected DatatableService $datatableServices,
        protected NotificationService $notificationServices,
    ) {}

    public function index()
    {
        $user = Auth::user();

        $clients = $user->roles->isNotEmpty() ? $this->services->index() : $this->services->index($user->id);

        if (request()->expectsJson()) {
            return $this->datatableServices->ajax($clients);
        }

        return view('client.index', [
            'page_title' => 'Clients',
            'columns' => $this->datatableServices->getColumns($clients),
        ]);
    }

    public function show(Client $client)
    {
        $application = $client->application ?? null;

        return view('client.show', [
            'client' => $client,
            'application' => $application,
            'beneficiary' => $application->beneficiary ?? null,
            'page_title' => $client->fullname() . ' | Client | ' . ($application ? $application->tracking_no : 'Draft'),
        ]);
    }

    public function create()
    {
        $page_title = 'New Client Record';
        $matched = [];
        $user = Auth::user();
        $client = null;

        if ($user->hasRole('staff')) {
            return redirect()->route('dashboard')->with('warning', 'You are not allowed to apply as a client.');
        }

        if (session()->has('client_uuid')) {
            session()->remove('client_uuid');
        }

        if ($user->clients->count() === 0 && $user->citizen_uuid !== null) {
            $this->citizenServices->checkIfUser('uuid', $user->citizen_uuid, true);
        } else if ($user->clients->count() > 0) {
            $client = $user->clients->first();
        }

        $citizen = session('citizen');

        if ($citizen) {
            $barangays = Barangay::pluck('name', 'id');
            $genders = Sex::pluck('name', 'id');
            $civilStatus = CivilStatus::pluck('name', 'id');

            if (isset($citizen['sex'])) {
                $matched['sex_id'] = $this->services->match($citizen['sex'], $genders, true);
            }

            if (isset($citizen['barangay'])) {
                $matched['barangay_id'] = $this->services->match($citizen['barangay'], $barangays, true);
            }

            if (isset($citizen['civil_status'])) {
                $matched['civil_id'] = $this->services->match($citizen['civil_status'], $civilStatus, false);
            }
        }

        $view = view('client.create', [
            'page_title' => $page_title,
        ]);

        if (!$client && !$citizen) {
            session()->flash('info', 'The system could not prefill some fields. We apolagize for the inconvinience.');
        }

        return $view->with([
            'client' => $client,
            'matched' => $matched,
        ]);
    }

    public function store(ClientRequest $request)
    {
        try {
            $client = $this->services->store($request->validated());

            session()->put('client_uuid', $client->uuid);

            return redirect()
                ->route('beneficiary.create')
                ->with('success', 'Client information added successfully! You may edit the information later when finalizing your application.');

            // $result = $this->clientServices->storeClient($request->validated(), Auth::user(), $request->file('images', []));
            // $client = $result['client'] ?? null;

            // if (! $client) {
            //     return redirect()->back()->with('error', 'Failed to add client information!');
            // }

            // $ip = request()->ip();
            // $browser = request()->header('User-Agent');
            // activity()
            //     ->withProperties(['ip' => $ip, 'browser' => $browser])
            //     ->log('Added the client details: '.$client->id.(($result['uploadError'] ?? false) ? ' images failed to upload' : ''));

            // return redirect()
            //     ->route('client.show', $client)
            //     ->with('success', 'Client information added successfully!'.(($result['uploadError'] ?? false) ? ' However, some images failed to upload.' : '').' Please remember to bring hard copies of the submitted documents during the interview at the CSWDO Office.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to add client information!'.(app()->hasDebugModeEnabled() ? ': '.$e->getMessage() : ''));
        }
    }

    public function edit(Client $client)
    {
        return view('client.edit', [
            'client' => $client,
            'page_title' => 'Edit ' . $client->fullname(),
        ]);
    }

    public function update(UpdateClientRequest $request, Client $client)
    {
        try {
            $this->services->update($request->validated(), $client);
            activity()
                ->withProperties(['ip' => request()->ip(), 'browser' => request()->userAgent(), 'client' => $client->uuid])
                ->causedBy(Auth::user())
                ->log('Updated the client details: '.$client->uuid);

            return redirect()
                ->route('client.show', $client)
                ->with('success', 'Client information updated successfully!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to update client information!'.(app()->hasDebugModeEnabled() ? ': '.$e->getMessage() : ''));
        }
    }

    public function destroy(Client $client)
    {
        $client = $this->clientServices->deleteClient($client);

        activity()
            ->causedBy(Auth::user())
            ->log('Deleted a client details: '.$client->id);

        return redirect()
            ->route('client.index')
            ->with('success', 'Client information deleted successfully!');
    }

    public function assessment(Request $request, $id)
    {
        try {
            $client = Client::findOrFail($id);
            $this->authorize('create', [ClientAssessment::class, $client]);

            $request->validate([
                'problem_presented' => 'required|string|max:255',
                'assessment' => 'required|string|max:255',
            ]);

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
                ->log('Added an assessment for a client');

            return redirect()->back()->with('success', 'Assessment created successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function recommendedService(Request $request, $id)
    {
        try {
            $client = Client::findOrFail($id);
            $this->authorize('create', [ClientRecommendation::class, $client]);

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

                $this->clientServices->transferClient($client->id);

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
            } elseif ($request['type'] == 'libreng_libing') {
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
                    'type' => 'libreng_libing',
                    'remarks' => $request['remarks'],
                ]);

                $this->clientServices->transferClient($client->id);

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
}
