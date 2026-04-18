<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClaimantChangeRequest;
use App\Models\BurialAssistance;
use App\Models\ClaimantChange;
use App\Models\ProcessLog;
use App\Models\User;
use App\Services\CentralClientService;
use App\Services\ClaimantChangeService;
use App\Services\ClaimantService;
use Illuminate\Http\Request;
use Str;

class ClaimantChangeController extends Controller
{
    protected $claimantChangeService;

    protected $claimantService;

    protected $clientService;

    protected $centralClientService;

    public function __construct(ClaimantChangeService $claimantChangeService, ClaimantService $claimantService, CentralClientService $centralClientService)
    {
        $this->claimantChangeService = $claimantChangeService;
        $this->claimantService = $claimantService;
        $this->clientService = $centralClientService;
    }

    public function store(StoreClaimantChangeRequest $request, $id)
    {
        try {
            $new_claimant_email = $request->input('email');
            if (! $new_claimant_id = User::where('email', $new_claimant_email)->first()->id) {
                return redirect()->back()->with('error', 'The new claimant does not have an account in the system. Please notify them to access this system once with their TLC Portal account to proceed.');
            }

            $validated = $request->validated();

            if ($validated) {
                $burialAssistance = BurialAssistance::findOrFail($id);
                $validated['burial_assistance_id'] = $burialAssistance->id;

                if (! $burialAssistance->claimant) {
                    return redirect()->back()->with('error', 'Unable to find claimant.');
                }

                if ($burialAssistance->status == 'approved' || $burialAssistance->status == 'rejected' || $burialAssistance->status == 'released') {
                    return redirect()->back()->with('error', 'Changing claimant is not allowed in a '.$burialAssistance->status.' status.');
                }

                $validated['old_claimant_id'] = $burialAssistance->claimant->id;
                $validated['id'] = (string) Str::uuid();
                $newClaimant = $this->claimantService->store($validated);

                $validated['new_claimant_id'] = $newClaimant->id;
                $validated['new_claimant_user_id'] = $new_claimant_id;
                $this->claimantChangeService->store($validated);

                $ip = request()->ip();
                $browser = request()->header('User-Agent');
                activity()
                    ->causedBy(null)
                    ->withProperties(['ip' => $ip, 'browser' => $browser])
                    ->log('Request for claimant change submitted by guest');

                return redirect()
                    ->route('burial.show', ['id' => $id])
                    ->with('success', 'Your request for claimant change has been submitted successfully. Please wait for the approval.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to submit claimant change.'.(app()->hasDebugModeEnabled() ? ': '.$e->getMessage() : ''));
        }
    }

    public function decide(Request $request, $uuid, $change)
    {
        $change = ClaimantChange::where('burial_assistance_id', $uuid)
            ->where('id', $change)
            ->firstOrFail();
        if (! $change) {
            return redirect()->back()->withErrors(['error' => 'Claimant Change not found.']);
        }

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
                'comments' => 'Change of claimant has been approved. Progress has been reset to evaluate the new claimant.',
                'is_progress_step' => false,
            ]);
        } elseif ($request->decision == 'rejected') {
            $change->update([
                'status' => 'rejected',
            ]);
        }

        return back()->with('success', 'Claimant change '.($request->decision === 'approve' ? 'approved' : 'rejected').' successfully.');
    }
}
