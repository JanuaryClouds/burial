<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClaimantChangeRequest;
use App\Models\Claimant;
use Illuminate\Http\Request;
use App\Models\ClaimantChange;
use App\Models\BurialAssistance;
use App\Models\ProcessLog;
use Str;

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
                "id" => Str::uuid(),
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
                $ip = request()->ip();
                $browser = request()->header('User-Agent');
                activity()
                ->causedBy(null)
                ->withProperties(['ip' => $ip, 'browser' => $browser])
                ->log('Request for claimant change submitted by guest');
                
                return redirect()
                    ->route('guest.burial-assistance.track-page', ['code' => $burialAssistance->tracking_code])
                    ->with("alertSuccess", "Your request for claimant change has been submitted successfully. Please wait for the approval.");
            } else {
                return redirect()->back()->with("error","Failed to submit claimant change.");
            }
        }
    }

    public function decide(Request $request, $applicationId, $changeId) {
        $change = ClaimantChange::where('burial_assistance_id', $applicationId)
            ->where('id', $changeId)
            ->firstOrFail();

        if ($request->decision == 'approved') {
            $change->update([
                'status' => 'approved',
                'changed_at' => now(),
            ]);

            ProcessLog::create([
                'id' => Str::uuid(),
                'burial_assistance_id' => $change->burialAssistance->id,
                'claimant_id' => $change->newClaimant->id,
                'loggable_id' => $change->id,
                'loggable_type' => ClaimantChange::class,
                'date_in' => now(),
                'comments' => 'Change of claimant has been approved',
                'is_progress_step' => false
            ]);

            // $change->burialAssistace->processLogs::create([
            //     'burial_assistance_id' => $change->burialAssistance->id,
            //     'loggable_id' => $change->id,
            //     'loggable_type' => ClaimantChange::class,
            //     'date_in' => now(),
            //     'comments' => 'Claimant change approved by ' . auth()->user()->name,
            //     'is_progress_step' => false
            // ]);
        } elseif ($request->decision == 'rejected') {
            $change->update([
                'status' => 'rejected',
            ]);

            ProcessLog::create([
                'id' => Str::uuid(),
                'burial_assistance_id' => $change->burialAssistance->id,
                'claimant_id' => $change->newClaimant->id,
                'loggable_id' => $change->id,
                'loggable_type' => ClaimantChange::class,
                'date_in' => now(),
                'comments' => 'Change of claimant has been rejected',
                'is_progress_step' => false
            ]);
        }
        
        return back()->with('success','Claimant change ' . ($request->decision === 'approve' ? 'approved' : 'rejected') . ' successfully.');
    }
}
