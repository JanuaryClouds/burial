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
use DB;
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
            $newClaimantUserId = $request->input('new_claimant_user_id');
            if (! $newClaimantUser = User::where('id', $newClaimantUserId)->first()) {
                return redirect()->back()->with('error', 'Couldn\'t find the new claimant as a user in this system. Please ensure they are a previous client using their TLC Portal account.');
            }

            if ($newClaimantUser->roles()->exists()) {
                return redirect()->back()->with('error', 'Users with system roles cannot be set as claimants, Please select a different user.');
            }

            $validated = $request->validated();

            if ($validated) {
                $burialAssistance = BurialAssistance::findOrFail($id);

                if (! $burialAssistance->originalClaimant()) {
                    return redirect()->back()->with('error', 'Unable to find claimant.');
                }

                if (! app()->isLocal()) {
                    if (auth()->user()->id != $burialAssistance->originalClaimant()?->client?->user_id) {
                        return redirect()->back()->with('error', 'You are not authorized to change the claimant.');
                    }
                }

                if ($newClaimantUser->id == $burialAssistance->originalClaimant()?->client?->user_id) {
                    return redirect()->back()->with('info', 'You are already the current claimant of this assistance.');
                }

                if ($burialAssistance->status == 'approved' || $burialAssistance->status == 'rejected' || $burialAssistance->status == 'released') {
                    return redirect()->back()->with('error', 'Changing claimant is not allowed in a '.$burialAssistance->status.' status.');
                }

                $validated['burial_assistance_id'] = $burialAssistance->id;
                $validated['old_claimant_id'] = $burialAssistance->originalClaimant()?->id;
                $validated['id'] = (string) Str::uuid();
                $validated['first_name'] = $newClaimantUser->first_name;
                $validated['middle_name'] = $newClaimantUser->middle_name ?? null;
                $validated['last_name'] = $newClaimantUser->last_name;
                $validated['suffix'] = $newClaimantUser->suffix ?? null;
                $validated['contact_number'] = $newClaimantUser->contact_number ?? null;
                $newClaimant = $this->claimantService->store($validated);

                $validated['new_claimant_id'] = $newClaimant->id;
                $validated['new_claimant_user_id'] = $newClaimantUser->id;
                $claimantChange = $this->claimantChangeService->store($validated);
                
                // After a meeting regarding how changing claimants is done, only admins can change claimants that are automatically approved.
                $claimantChange->status = 'approved';
                $claimantChange->changed_at = now();
                $claimantChange->save();

                ProcessLog::create([
                    'id' => Str::uuid(),
                    'burial_assistance_id' => $claimantChange->burialAssistance->id,
                    'claimant_id' => $newClaimant->id,
                    'loggable_id' => $claimantChange->id,
                    'loggable_type' => ClaimantChange::class,
                    'date_in' => now(),
                    'comments' => 'Burial Assistance claimant has been changed. Progress has been reset to evaluate the new claimant.',
                    'is_progress_step' => false,
                ]);

                $citizen_uuid = $newClaimantUser->citizen_uuid;

                if (! $citizen_uuid) {
                    return redirect()->back()->with('error', 'New claimant does not have an account in the system.');
                }

                $this->notificationService->send(
                    $citizen_uuid,
                    'claimant_change_approved',
                    'Claimant Change Approved',
                    'You have been set as the new claimant for a burial assistance application.'
                );

                $ip = request()->ip();
                $browser = request()->header('User-Agent');
                activity()
                    ->causedBy(auth()->user())
                    ->withProperties([
                        'ip' => $ip,
                        'browser' => $browser,
                        'burialAssistance' => $burialAssistance->id,
                    ])
                    ->log('Changed claimants to a burial assistance');

                return redirect()
                    ->route('burial.show', ['id' => $id])
                    ->with('success', 'Claimants have been changed.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to submit claimant change.'.(app()->hasDebugModeEnabled() ? ': '.$e->getMessage() : ''));
        }
    }

    public function decide(Request $request, $uuid, $change)
    {
        DB::transaction(function () use ($request, $uuid, $change) {
            $change = ClaimantChange::where('burial_assistance_id', $uuid)
                ->where('id', $change)
                ->firstOrFail();

            $request->validate([
                'decision' => 'required|in:approved,rejected',
            ]);

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

                $citizen_uuid = $change->newUserClaimant?->citizen_uuid;

                if (! $citizen_uuid) {
                    return redirect()->back()->with('error', 'New claimant does not have an account in the system.');
                }

                $this->notificationService->send(
                    $citizen_uuid,
                    'claimant_change_approved',
                    'Claimant Change Approved',
                    'You have been set as the new claimant for a burial assistance application.'
                );

                $ip = request()->ip();
                $browser = request()->header('User-Agent');
                activity()
                    ->causedBy(auth()->user())
                    ->withProperties(['ip' => $ip, 'browser' => $browser, 'claimantChange' => $change->id])
                    ->log('Approved claimant change');
            } elseif ($request->decision == 'rejected') {
                $change->update([
                    'status' => 'rejected',
                ]);

                $citizen_uuid = $change->oldClaimant?->client?->user?->citizen_uuid;

                if (! $citizen_uuid) {
                    return redirect()->back()->with('error', 'Old claimant does not have an account in the system.');
                }

                $this->notificationService->send(
                    $citizen_uuid,
                    'claimant_change_rejected',
                    'Claimant Change Rejected',
                    'Your request to change claimants in a burial assistance application has been rejected.'
                );

                $ip = request()->ip();
                $browser = request()->header('User-Agent');
                activity()
                    ->causedBy(auth()->user())
                    ->withProperties(['ip' => $ip, 'browser' => $browser, 'claimantChange' => $change->id])
                    ->log('Rejected claimant change');
            }

        });

        return back()->with('success', 'Claimant change '.($request->decision === 'approved' ? 'approved' : 'rejected').' successfully.');
    }
}
