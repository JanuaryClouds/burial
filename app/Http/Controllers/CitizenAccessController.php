<?php

namespace App\Http\Controllers;

use App\Models\ApplicationStep;
use App\Models\BurialAssistance;
use App\Models\Client;
use App\Models\DocumentRequirement;
use App\Models\FuneralAssistance;
use App\Services\CentralClientService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class CitizenAccessController extends Controller
{
    protected $centralClientService;

    public function __construct(CentralClientService $centralClientService)
    {
        $this->centralClientService = $centralClientService;
    }

    public function index(Request $request)
    {
        $page_title = 'Funeral Assistance System - Taguig CSWDO';
        $uuid = $request->query('uuid');
        // Clear citizen session if no uuid is provided, or if it's a debug/logout request.
        if ($uuid === 'debug' || $uuid === 'logout') {
            session(['citizen' => null]);
            $uuid = null; // Ensure uuid is null for the rest of the logic.
        }

        $citizen = session('citizen');

        $finishedBurialAssistances = BurialAssistance::where('status', 'released')->count();
        $finishedFuneralAssistances = FuneralAssistance::whereNotNull('forwarded_at')->count();
        $totalClients = $finishedBurialAssistances + $finishedFuneralAssistances;
        $steps = ApplicationStep::steps();
        $burialDocuments = DocumentRequirement::burial();
        $funeralDocuments = DocumentRequirement::funeral();

        $recentClients = Client::where(function ($query) {
            $query->where('created_at', '>=', Carbon::now()->subDays(7));
        })->count();

        // If a new UUID is provided that is different from the one in the session,
        // clear the old citizen data to force a re-fetch.
        if ($uuid && session()->has('citizen') && session('citizen')['user_id'] != $uuid) {
            session(['citizen' => null]);
        }

        if (is_null(session('citizen')) && $uuid) {
            try {
                $citizen = $this->centralClientService->fetchCitizen($uuid);

                if ($citizen) {
                    // Store citizen data in session to keep them "logged in"
                    session(['citizen' => $citizen]);
                } else {
                    return back()->with('info', 'Unable to fetch citizen details.');
                }
            } catch (Exception $e) {
                return back()->with('error', $e->getMessage());
            }
        }

        if ($citizen) {
            $existingClient = Client::where('citizen_id', session('citizen')['user_id'])->exists();
        } else {
            $existingClient = false;
        }

        $cardData = [
            [
                'label' => 'Recent Clients',
                'icon' => 'ki-support-24',
                'pathsCount' => 3,
                'count' => $recentClients,
            ],
            [
                'label' => 'Burial Assistances Provided',
                'icon' => 'ki-document',
                'pathsCount' => 2,
                'count' => $finishedBurialAssistances,
            ],
            [
                'label' => 'Funeral Assistances Provided',
                'icon' => 'ki-file-up',
                'pathsCount' => 2,
                'count' => $finishedFuneralAssistances,
            ],
            [
                'label' => 'Total Clients Served',
                'icon' => 'ki-people',
                'pathsCount' => 5,
                'count' => $totalClients,
            ],
        ];

        return view('landing', compact(
            'citizen',
            'existingClient',
            'steps',
            'burialDocuments',
            'funeralDocuments',
            'page_title',
            'cardData'
        ));
    }
}
