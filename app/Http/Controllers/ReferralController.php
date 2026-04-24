<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReferralRequest;
use App\Http\Requests\UpdateReferralRequest;
use App\Models\Client;
use App\Models\Referral;
use App\Services\NotificationService;
use App\Services\ReferralService;

class ReferralController extends Controller
{
    protected $referralServices;
    protected $notificationServices;

    public function __construct(ReferralService $referralService, NotificationService $notificationService)
    {
        $this->referralServices = $referralService;
        $this->notificationServices = $notificationService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
                    ->performedOn($client)
                    ->withProperties(['ip' => $ip, 'browser' => $browser])
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
