<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateBeneficiaryFamilyRequest;
use App\Models\BeneficiaryFamily;
use App\Services\BeneficiaryFamilyService;

class BeneficiaryFamilyController extends Controller
{
    protected $beneficiaryFamilyServices;

    public function __construct(BeneficiaryFamilyService $beneficiaryFamilyServices)
    {
        $this->beneficiaryFamilyServices = $beneficiaryFamilyServices;
    }

    public function show(string $id)
    {
        $family = BeneficiaryFamily::findOrFail($id);
        $page_title = 'Edit '.$family->name;
        $readonly = ! auth()->user()->hasRole('superadmin');

        return view('beneficiary.family.view', compact('family', 'page_title', 'readonly'));
    }

    public function update(UpdateBeneficiaryFamilyRequest $request, string $id)
    {
        try {
            $this->beneficiaryFamilyServices->update($request->validated(), $id);
            activity()
                ->withProperties(['ip' => $request->ip(), 'beneficiary_family' => $id])
                ->causedBy(auth()->user())
                ->log('Updated beneficiary family');

            return back()->with('success', 'Beneficiary family updated successfully.');
        } catch (\Throwable $th) {
            return back()->with('error', 'Beneficiary family updated failed.'.(app()->hasDebugModeEnabled() ? $th->getMessage() : ''));
        }
    }
}
