<?php

namespace App\Http\Controllers;

use App\Http\Requests\BurialServiceRequest;
use Illuminate\Http\Request;
use App\Http\Requests\BurialAssistanceReqRequest;
use App\Services\BurialAssistanceRequestService;
use App\Models\BurialAssistanceRequest;
use Validator;
use Illuminate\Support\Str;

class BurialAssistanceRequestController extends Controller
{
    protected $burialAssistanceRequestService;

    public function __construct(BurialAssistanceRequestService $burialAssistanceRequestService)
    {
        $this->burialAssistanceRequestService = $burialAssistanceRequestService;
    }

    public function store(BurialAssistanceReqRequest $request)
    {
        $data = $request->validated();
        $data['uuid'] = Str::uuid()->toString();
        $validator = Validator::make($request->all(), [
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            // Return the validation errors
            return response()->json(['errors' => $validator->messages()], 422);
        }
        
        $serviceRequest = $this->burialAssistanceRequestService->store($data);
        $filename = "burial-assistance-request-{$serviceRequest->id}";
        
        if ($serviceRequest && $request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {
                $filenameWithIndex = "{$filename}-{$index}." . $file->getClientOriginalExtension();
                $file->storeAs('death_certificates', $filenameWithIndex, 'public');
            }

            return redirect()->route('guest.request.submit.success')->with('success', 'Burial Assistance Request created successfully.')->with('service', $serviceRequest->uuid);
        } else {
            return redirect()->back()->withErrors(['error' => 'Failed to create Burial Assistance Request.']);
        }
        
    }

    public function track(Request $request) {
        $request->validate([
            'uuid' => 'required|uuid|exists:burial_assistance_requests,uuid',
        ]);

        $serviceRequest = BurialAssistanceRequest::where('uuid', $request->uuid)->first();
        if (!$serviceRequest) {
            return redirect()->back()->withErrors(['error' => 'Burial Assistance Request not found.']);
        }
        // dd($serviceRequest);
        return view('guest.request_tracker', compact('serviceRequest'));
    }
}
