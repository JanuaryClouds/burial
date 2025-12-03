<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClaimantChangeRequest;
use App\Models\Claimant;
use Illuminate\Http\Request;
use App\Models\ClaimantChange;
use App\Models\BurialAssistance;
use App\Models\ProcessLog;
use App\Services\ClaimantChangeService;
use App\Services\ClaimantService;
use App\Services\ClientService;
use App\Services\CentralClientService;
use Str;

class ClaimantChangeController extends Controller
{
    protected $claimantChangeService;
    protected $claimantService;
    protected $clientService;
    protected $centralClientService;

    public function __construct(ClaimantChangeService $claimantChangeService, ClaimantService $claimantService, CentralClientService $centralClientService) {
        $this->claimantChangeService = $claimantChangeService;
        $this->claimantService = $claimantService;
        $this->clientService = $centralClientService;
    }

    public function store(StoreClaimantChangeRequest $request, $id) {
        // dd($request->all(), $id);
        $validated = $request->validated();

        if ($validated) {
            $burialAssistance = BurialAssistance::findOrFail($id);
            $validated['burial_assistance_id'] = $burialAssistance->id;
            $validated['old_claimant_id'] = $burialAssistance->claimant_id;
            $validated['id'] = Str::uuid();

            // $newClaimant = Claimant::create([
            //     "id" => Str::uuid(),
            //     "first_name" => $validated['claimant']['first_name'],
            //     "middle_name" => $validated['claimant']['middle_name'],
            //     "last_name" => $validated['claimant']['last_name'],
            //     "suffix" => $validated['claimant']['suffix'],
            //     "relationship_to_deceased" => $validated['claimant']['relationship_to_deceased'],
            //     "mobile_number" => $validated['claimant']['mobile_number'],
            //     "address" => $validated['claimant']['address'],
            //     "barangay_id" => $validated['claimant']['barangay_id'],
            // ]);

            $validated['relationship_to_deceased'] = $validated['relationship_id'];
            $validated['address'] = $validated['house_no'] . ' ' . $validated['street'];
            $validated['mobile_number'] = $validated['contact_no'];
            if (env('APP_DEBUG')) {
                $validated['client_id'] = '62c2569a-5951-40b7-8d99-ac50d19e5896';                
            } else {
                $validated['client_id'] = session('citizen')['user_id'];
            }

            $newClaimant = $this->claimantService->store($validated);
            
            $validated['new_claimant_id'] = $newClaimant->id;
            $claimantChange = $this->claimantChangeService->store($validated);
            
            if( $claimantChange ) {
                $ip = request()->ip();
                $browser = request()->header('User-Agent');
                activity()
                ->causedBy(null)
                ->withProperties(['ip' => $ip, 'browser' => $browser])
                ->log('Request for claimant change submitted by guest');
                
                return redirect()
                    ->route('landing.page')
                    ->with("alertSuccess", "Your request for claimant change has been submitted successfully. Please wait for the approval.");
            } else {
                return redirect()->back()->with("error","Failed to submit claimant change.");
            }
        }
    }

    public function form(Request $request, $uuid) {
        $burialAssistance = BurialAssistance::findOrFail($uuid);
        $uuid = $request->query('uuid') ?? false;

        if ($uuid) {
            $citizen = $this->centralClientService->fetchByClient($uuid);
        } else {
            return redirect()->route('landing.page');
        }

        return view('burial.change-claimant', compact('burialAssistance'));
    }

    public function decide(Request $request, $uuid, $change) {
        $change = ClaimantChange::where('burial_assistance_id', $uuid)
            ->where('id', $change)
            ->firstOrFail();
        if (!$change) {
            return redirect()->back()->withErrors(['error' => 'Claimant Change not found.']);
        }

        dd($change);
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
