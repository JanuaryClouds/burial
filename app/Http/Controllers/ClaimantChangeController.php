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
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Str;

class ClaimantChangeController extends Controller
{
    protected $claimantChangeService;

    protected $claimantService;

    protected $clientService;

    protected $centralClientService;

    protected $notificationService;

    public function __construct(ClaimantChangeService $claimantChangeService, ClaimantService $claimantService, CentralClientService $centralClientService, NotificationService $notificationService)
    {
        $this->claimantChangeService = $claimantChangeService;
        $this->claimantService = $claimantService;
        $this->clientService = $centralClientService;
        $this->notificationService = $notificationService;
    }

    public function store(StoreClaimantChangeRequest $request, $id)
    {
        try {
            $new_claimant_email = $request->input('email');
            if (! $new_claimant = User::where('email', $new_claimant_email)->first()) {
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
                $validated['first_name'] = $new_claimant->first_name;
                $validated['middle_name'] = $new_claimant->middle_name ?? null;
                $validated['last_name'] = $new_claimant->last_name;
                $validated['suffix'] = $new_claimant->suffix ?? null;
                $validated['contact_number'] = $new_claimant->contact_number ?? null;
                $newClaimant = $this->claimantService->store($validated);

                $validated['new_claimant_id'] = $newClaimant->id;
                $validated['new_claimant_user_id'] = $new_claimant->id;
                $this->claimantChangeService->store($validated);

                $ip = request()->ip();
                $browser = request()->header('User-Agent');
                activity()
                    ->causedBy(auth()->user())
                    ->performedOn($burialAssistance)
                    ->withProperties(['ip' => $ip, 'browser' => $browser])
                    ->log('Request for claimant change submitted');

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

            $this->notificationService->send(
                $change->newClaimant->user->citizen_uuid,
                'claimant_change_approved',
                'Claimant Change Approved',
                'You have been set as the new claimant for a burial assistance application.'
            );

            $ip = request()->ip();
            $browser = request()->header('User-Agent');
            activity()
                ->causedBy(auth()->user())
                ->performedOn($change)
                ->withProperties(['ip' => $ip, 'browser' => $browser])
                ->log('Approved claimant change');
        } elseif ($request->decision == 'rejected') {
            $change->update([
                'status' => 'rejected',
            ]);

            $this->notificationService->send(
                $change->oldClaimant->user->citizen_uuid,
                'claimant_change_rejected',
                'Claimant Change Rejected',
                'Your request to change claimants in a burial assistance application has been rejected.'
            );

            $ip = request()->ip();
            $browser = request()->header('User-Agent');
            activity()
                ->causedBy(auth()->user())
                ->performedOn($change)
                ->withProperties(['ip' => $ip, 'browser' => $browser])
                ->log('Rejected claimant change');
        }

        return back()->with('success', 'Claimant change '.($request->decision === 'approved' ? 'approved' : 'rejected').' successfully.');
    }
}
