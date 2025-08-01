<?php

namespace App\Http\Controllers;

use App\Http\Requests\BurialServiceRequest;
use Illuminate\Http\Request;
use App\Http\Requests\BurialAssistanceReqRequest;
use App\Services\BurialAssistanceRequestService;
use App\Models\BurialAssistanceRequest;
use Illuminate\Support\Facades\Storage;
use Validator;
use Illuminate\Support\Facades\Crypt;
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
        
        if ($serviceRequest && $request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {
                $extension = $file->getClientOriginalExtension();
                $filename = "{$serviceRequest->uuid}-{$index}.".$extension;

                $encryptedFile = Crypt::encrypt($file->getContent());
                Storage::put('/public/death_certificates/' . $filename, $encryptedFile);
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

    public function index() {
        $serviceRequests = BurialAssistanceRequest::query()->simplePaginate(10);
        return view('admin.burial-requests', compact('serviceRequests'));
    }
}
