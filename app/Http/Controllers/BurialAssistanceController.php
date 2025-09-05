<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBurialAssistanceRequest;
use Illuminate\Http\Request;
use App\Models\Barangay;
use App\Models\Relationship;
use App\Models\Claimant;
use App\Models\Deceased;
use App\Models\BurialAssistance;
use Str;

class BurialAssistanceController extends Controller
{
    public function view() {
        $barangays = Barangay::all();
        $relationships = Relationship::all();
        return view('guest.burial-assistance.view', compact(
            'barangays',
            'relationships',
        ));
    }

    public function store(StoreBurialAssistanceRequest $request) {
        $validated = $request->validated();
        $claimant = Claimant::create($validated['claimant']);
        $deceased = Deceased::create($validated['deceased']);

        $burialAssistance = BurialAssistance::create([
            'tracking_code' => strtoupper(Str::random(6)),
            'application_date' => now(),
            'claimant_id' => $claimant->id,
            'deceased_id' => $deceased->id,
            'funeraria' => $validated['burial_assistance']['funeraria'],
            'remarks' => $validated['burial_assistance']['remarks'],
        ]);

        return redirect()->route('landing.page')
            ->with(
                'success', 
                'Successfully submitted burial assistance application. Please check your messages for the assistance\'s tracking code. You can use the given code to track the progress of the assistance application.'
            );
    }

    public function track(Request $request) {
        $request->validate([
            'tracking_code' => 'required|string|exists:burial_assistances,tracking_code',
            'mobile_number' => 'required|string',
        ]);

        $burialAssistance = BurialAssistance::where('tracking_code', $request->tracking_code)->first();
        if (!$burialAssistance) {
            return redirect()->back()->with([
                'error'=> 'Burial Assistance Application not found.'
            ]);
        } elseif ($request->mobile_number != substr($burialAssistance->claimant->mobile_number, -4)) {
            return redirect()->back()->with([
                'info' => 'Mobile number does not match. Please try again.'
            ]);
        }

        // dd($burialAssistance);

        if (!auth()->check()) {
            $burialAssistance->claimant->first_name = Str::mask($burialAssistance->claimant->first_name, '*', 3);
            $burialAssistance->claimant->middle_name = Str::mask($burialAssistance->claimant->middle_name, '*', 3);
            $burialAssistance->claimant->last_name = Str::mask($burialAssistance->claimant->last_name, '*', 3);
            $burialAssistance->claimant->mobile_number = Str::mask($burialAssistance->claimant->mobile_number, '*', 4, 3);
            $burialAssistance->claimant->address = Str::mask($burialAssistance->claimant->address, '*', 3);
        }

        $ip = request()->ip();
        $browser = request()->header('User-Agent');
        activity()
            ->causedBy(null)
            ->withProperties(['ip' => $ip, 'browser' => $browser])
            ->log('Burial Assistance tracked by guest');

        return view('guest.burial-assistance.tracker', compact('burialAssistance'));
    }
}
