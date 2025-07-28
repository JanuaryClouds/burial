<?php

namespace App\Http\Controllers;

use App\Http\Requests\BurialServiceRequest;
use Illuminate\Http\Request;
use Validator;
use App\Services\BurialServiceService;

class BurialServiceController extends Controller
{
    protected $BurialServiceService;

    public function __construct(BurialServiceService $burialServiceService) {
        $this->BurialServiceService = $burialServiceService;
    }

    public function new() {
        return view("admin.newBurialService");
    }

    public function store(BurialServiceRequest $request) {
        $data = $request->validated();
        $validator = Validator::make($request->all(), [
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            // Return the validation errors
            return response()->json(['errors' => $validator->messages()], 422);
        }
        
        $service = $this->BurialServiceService->store($data);
        $filename = "burial-service-{$service->id}-" . time();
        
        if ($service && $request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {
            $filenameWithIndex = "{$filename}-{$index}." . $file->getClientOriginalExtension();
            $file->storeAs('burial_images', $filenameWithIndex, 'public');
        }

        return redirect()->route('admin.burial.history')->with('success', 'Burial service created successfully.');
    }
        return redirect()->back()->withErrors(['error' => 'Failed to create burial service.']);
    }

    public function history() {
        // TODO: Fetch all histories of burial services
        return view("admin.burialServiceHistory");
    }

    public function providers() {
        // TODO: Fetch all burial service providers
        return view("admin.burialServiceProviders");
    }

    
}
