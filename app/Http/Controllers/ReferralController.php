<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReferralRequest;
use App\Http\Requests\UpdateReferralRequest;
use App\Models\Client;
use App\Models\Referral;
use App\Services\DatatableService;
use App\Services\NotificationService;
use App\Services\ReferralService;

class ReferralController extends Controller
{
    protected $referralServices;

    protected $notificationServices;

    protected $datatableServices;

    public function __construct(ReferralService $referralService, NotificationService $notificationService, DatatableService $datatableService)
    {
        $this->referralServices = $referralService;
        $this->notificationServices = $notificationService;
        $this->datatableServices = $datatableService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $page_title = 'Referrals';
        if (auth()->user()->roles()->exists()) {
            $data = $this->referralServices->index();
            $columns = $this->datatableServices->getColumns($data);
        } else {
            $data = $this->referralServices->index(auth()->user()->id);
            $columns = $this->datatableServices->getColumns($data, ['client']);
        }

        if (request()->expectsJson()) {
            return response()->json([
                'data' => $data->values(),
            ]);
        }

        return view('referral.index', compact('data', 'columns', 'page_title'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReferralRequest $request, $client_id)
    {
        try {
            $client = Client::findOrFail($client_id);
            $referral = $this->referralServices->store($request->validated(), $client->id);

            if ($referral) {
                $ip = request()->ip();
                $browser = request()->header('User-Agent');

                if ($client->user->citizen_uuid) {
                    $this->notificationServices->send(
                        $client->user->citizen_uuid,
                        'referral',
                        'New Referral',
                        'A referral has been given to you by the Taguig City CSWDO.'
                    );
                }

                activity()
                    ->causedBy(auth()->user())
                    ->withProperties(['ip' => $ip, 'browser' => $browser, 'client' => $client->id])
                    ->log('Created a referral');

                return redirect()->back()->with('success', 'Referral created successfully.');
            }

            return redirect()->back()->with('error', 'Failed to create a referral.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create a referral.'.(app()->hasDebugModeEnabled() ? ': '.$e->getMessage() : ''));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Referral $referral)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Referral $referral)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateReferralRequest $request, Referral $referral)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Referral $referral)
    {
        //
    }
}
