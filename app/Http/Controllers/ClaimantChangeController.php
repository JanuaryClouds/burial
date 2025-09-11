<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClaimantChangeRequest;
use App\Models\Claimant;
use Illuminate\Http\Request;
use App\Models\ClaimantChange;
use App\Models\BurialAssistance;

class ClaimantChangeController extends Controller
{
    public function store(StoreClaimantChangeRequest $request, $id) {
        // dd($request->all(), $id);
        $validated = $request->validated();

        if ($validated) {
            $burialAssistance = BurialAssistance::findOrFail($id);
            $validated['burial_assistance_id'] = $burialAssistance->id;
            $validated['old_claimant_id'] = $burialAssistance->claimant_id;
            
            $newClaimant = Claimant::create([
                "first_name" => $validated['claimant']['first_name'],
                "middle_name" => $validated['claimant']['middle_name'],
                "last_name" => $validated['claimant']['last_name'],
                "suffix" => $validated['claimant']['suffix'],
                "relationship_to_deceased" => $validated['claimant']['relationship_to_deceased'],
                "mobile_number" => $validated['claimant']['mobile_number'],
                "address" => $validated['claimant']['address'],
                "barangay_id" => $validated['claimant']['barangay_id'],
            ]);
            
            $validated['new_claimant_id'] = $newClaimant->id;
            // dd($validated);
            $claimantChange = ClaimantChange::create($validated);
            
            if( $claimantChange ) {
                return view('guest.burial-assistance.tracker', compact('burialAssistance'))->with("success","Claimant change submitted successfully.");
            } else {
                return redirect()->back()->with("error","Failed to submit claimant change.");
            }
        }
    }
}
