<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateBeneficiaryFamilyRequest;
use App\Models\BeneficiaryFamily;
use App\Services\BeneficiaryFamilyService;
use Illuminate\Support\Facades\Auth;

class BeneficiaryFamilyController extends Controller
{
    public function __construct(
        protected BeneficiaryFamilyService $services
    ) {}

    public function show(BeneficiaryFamily $member)
    {
        $application = $member->beneficiary->application;

        return view('beneficiary.family.show', [
            'page_title' => $member->name.' | Beneficiary Family Member | '.$application->tracking_no,
            'application' => $application,
            'member' => $member,
            'beneficiary' => $member->beneficiary,
        ]);
    }

    public function edit(BeneficiaryFamily $member)
    {
        return view('beneficiary.family.edit', [
            'page_title' => 'Edit '.$member->name,
            'member' => $member,
        ]);
    }

    public function update(UpdateBeneficiaryFamilyRequest $request, BeneficiaryFamily $member)
    {
        try {
            $this->services->update($request->validated(), $member);
            activity()
                ->withProperties(['ip' => request()->ip(), 'browser' => request()->userAgent(), 'beneficiary_family' => $member->uuid])
                ->causedBy(Auth::user())
                ->log('Updated beneficiary family');

            return back()->with('success', 'Beneficiary family updated successfully.');
        } catch (\Throwable $th) {
            return back()->with('error', 'Beneficiary family updated failed.'.(app()->hasDebugModeEnabled() ? $th->getMessage() : ''));
        }
    }
}
