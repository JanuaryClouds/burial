<?php

namespace App\Http\Controllers;

use App\Models\Referral;
use App\Http\Requests\StoreReferralRequest;
use App\Http\Requests\UpdateReferralRequest;
use App\Services\ReferralService;

class ReferralController extends Controller
{
    protected $referralServices;

    public function __construct(ReferralService $referralService)
    {
        $this->referralServices = $referralService;
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
            $this->referralServices->store($request->validated(), $client_id);
            return redirect()->back()->with('success', 'Referral created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create a referral.' . (app()->hasDebugModeEnabled() ? ': ' . $e->getMessage() : ''));
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
