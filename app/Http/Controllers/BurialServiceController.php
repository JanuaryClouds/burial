<?php

namespace App\Http\Controllers;

use App\Http\Requests\BurialServiceRequest;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\BurialServiceService;
use App\Models\BurialService;
use App\Models\Relationship;
use App\Models\Barangay;
use App\Models\BurialServiceProvider;
use App\Models\burialAssistanceRequest;

class BurialServiceController extends Controller
{
    protected $BurialServiceService;

    public function __construct(BurialServiceService $burialServiceService) {
        $this->BurialServiceService = $burialServiceService;
    }

    public function new() {
        return view("admin.newBurialService");
    }

    public function view($id) {
        $service = BurialService::where("id", $id)->first();
        $relationships = Relationship::getAllRelationships();
        $barangays = Barangay::getAllBarangays();
        $providers = BurialServiceProvider::getAllProviders();

        $disk = 'public';
        $folder = 'burial_images';
        $prefix = 'burial-service-' . $id . '-';

        $serviceImages = collect(Storage::disk($disk) ->files($folder))
        ->filter(function ($filePath) use ($prefix) {
            return str_starts_with(basename($filePath), $prefix);
        });

        return view('admin.view-service', compact('service', 'relationships', 'barangays', 'providers', 'serviceImages'));
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
        $filename = "burial-service-{$service->id}";
        
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
        return view("admin.burialServiceHistory");
    }

    public function providers() {
        return view("admin.burialServiceProviders");
    }

    // TODO
    public function requestToService($uuid) {
        $approvedAssistanceRequest = BurialAssistanceRequest::where('uuid', $uuid)->first();

        if (!$approvedAssistanceRequest) {
            return redirect()->route('admin.dashboard')->with('error','Unable to find burial assistance request.');
        }

        return view('admin.request-to-service', compact('approvedAssistanceRequest'));
    }

    // TODO: Add messaging API via email or SMS
    public function contact($id) {
        $success = true; // placeholder 

        if ($success) {
            return redirect()->route('admin.burial.history')->with('success','Successfuly messaged burial service representative.');
        }

        return redirect()->route('admin.burial.history')->with('error','Failed to message burial service representative.');
    }

    public function exportPDF($id) {
        $service = BurialService::findOrFail( $id );
        $relationships = Relationship::getAllRelationships();
        $barangays = Barangay::getAllBarangays();
        $providers = BurialServiceProvider::getAllProviders();

        $disk = 'public';
        $folder = 'burial_images';
        $prefix = 'burial-service-' . $id . '-';

        $serviceImages = collect(Storage::disk($disk) ->files($folder))
        ->filter(function ($filePath) use ($prefix) {
            return str_starts_with(basename($filePath), $prefix);
        });

        $pdf = Pdf::loadView('admin.printable-service-form', compact('service', 'relationships', 'barangays', 'providers', 'serviceImages'))
            ->setPaper('letter', 'portrait');

        // return $pdf->download("{$service->deceased_firstname} {$service->deceased_lastname}-burial-service-form.pdf");

        return view('admin.printable-service-form', compact('service', 'relationships', 'barangays', 'providers', 'serviceImages'));
    }
}
