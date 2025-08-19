<?php

namespace App\Http\Controllers;

use App\Exports\BurialAssistanceRequestsExport;
use App\Http\Requests\BurialServiceRequest;
use Illuminate\Http\Request;
use App\Http\Requests\BurialAssistanceReqRequest;
use App\Services\BurialAssistanceRequestService;
use App\Models\BurialAssistanceRequest;
use App\Models\BurialService;
use App\Models\Relationship;
use App\Models\Barangay;
use Illuminate\Support\Facades\Storage;
use Validator;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

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
        $existingRequest = BurialAssistanceRequest::where(function ($query) use ($request) {
        $query->where('deceased_lastname', $request->input('deceased_lastname'))
              ->where('deceased_firstname', $request->input('deceased_firstname'))
              ->where('burial_address', $request->input('burial_address'))
              ->where('barangay_id', $request->input('barangay_id'));
        })
        ->exists();

        if ($existingRequest) {
            return redirect()->route('landing.page')->with('info', "Burial Assistance Request for {$request->input('deceased_lastname')}, {$request->input('deceased_firstname')} already exists.");
        }

        $data['uuid'] = Str::uuid()->toString();
        
        if (count($request->file('images')) > 2) {
            return back()->withErrors(['images' => 'You can only upload up to 2 images.']);
        }
        $validator = Validator::make($request->all(), [
            'images' => 'required|array|min:1|max:2',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            // Return the validation errors
            return response()->json(['errors' => $validator->messages()], 422);
        }
        
        $serviceRequest = $this->burialAssistanceRequestService->store($data);
        
        if ($serviceRequest && $request->hasFile('images')) {
            $fileCount = 0; // Prevent the user from uploading too many files
            foreach ($request->file('images') as $index => $file) {
                if ($fileCount >= 2) {
                    break;
                }

                $extension = $file->getClientOriginalExtension();
                $filename = "{$serviceRequest->uuid}-{$index}.".$extension;

                $encryptedFile = Crypt::encrypt($file->getContent());
                Storage::put('/public/death_certificates/' . $filename, $encryptedFile);
                $fileCount++;
            }

            $ip = request()->ip();
            $browser = request()->header('User-Agent');
            activity()
                ->causedBy(null)
                ->withProperties(['ip' => $ip, 'browser' => $browser])
                ->log('Burial Assistance Request submitted by guest');

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
        
        if (!auth()->check()) {
            $serviceRequest->deceased_lastname = Str::mask($serviceRequest->deceased_lastname, '*', 3);
            $serviceRequest->deceased_firstname = Str::mask($serviceRequest->deceased_firstname, '*', 3);
            $serviceRequest->representative = Str::mask($serviceRequest->representative, '*', 3);
            $serviceRequest->representative_contact = Str::mask($serviceRequest->representative_contact, '*', 3);
            $serviceRequest->burial_address = Str::mask($serviceRequest->burial_address, '*', 3);
        }

        $ip = request()->ip();
        $browser = request()->header('User-Agent');
        activity()
            ->causedBy(null)
            ->withProperties(['ip' => $ip, 'browser' => $browser])
            ->log('Burial Assistance Request tracked by guest');

        // dd($serviceRequest);
        return view('guest.request_tracker', compact('serviceRequest'));
    }

    public function index() {
        return view('admin.burial-requests');
    }

    public function view($uuid) {
        $serviceRequest = BurialAssistanceRequest::where('uuid', $uuid)->first();
        $existingService = BurialService::where(function ($query)use ($serviceRequest) {
            $query
                ->where('deceased_lastname', $serviceRequest->deceased_lastname)
                ->where('deceased_firstname', $serviceRequest->deceased_firstname)
                ->where('burial_address', $serviceRequest->burial_address)
                ->where('barangay_id', $serviceRequest->barangay_id);
        })->exists();

        if (!$serviceRequest) {
            return redirect()->back()->withErrors(['error' => 'Burial Assistance Request not found.']);
        }
        $requestImageFileName = $serviceRequest->uuid;
        $requestImages = [];

        $disk = 'public';
        $folder = 'death_certificates';

        $allFiles = Storage::disk($disk)->files($folder);

        $matchedFiles = collect($allFiles)->filter(function ($filePath) use ($requestImageFileName) {
            return str_starts_with(basename($filePath), $requestImageFileName);
        });

        foreach ($matchedFiles as $filePath) {
            try {
                $encryptedContent = Storage::disk($disk)->get($filePath);
                $decryptedContent = Crypt::decrypt($encryptedContent);

                $requestImages[] = [
                    'filename' => basename($filePath),
                    'content' => $decryptedContent,
                ];
            } catch (\Exception $e) {
                // Optional: log or skip if decryption fails
                continue;
            }
        }

        $relationships = Relationship::getAllRelationships();
        $barangays = Barangay::getAllBarangays();
        return view('admin.manage-request', [
            'serviceRequest' => $serviceRequest,
            'relationships'=> $relationships,
            'barangays' => $barangays,
            'requestImages' => $requestImages,
            'existingService' => $existingService
        ]);
    }

    public function updateStatus(Request $request, $uuid) {
        $serviceRequest = BurialAssistanceRequest::where('uuid', $uuid)->first();
        if (!$serviceRequest) {
            return redirect()->back()->withErrors(['error' => 'Burial Assistance Request not found.']);
        } 

        $serviceRequest->status = $request->status;
        $serviceRequest->update();
        return redirect()->route('admin.burial.requests')->with('success', 'Status updated successfully.');
    }

    public function contact($uuid) {
        $success = true; // placeholder

        if ($success) {
            return redirect()->route('admin.burial.requests')->with('success', 'Successfully messaged burial assistance request.');
        }

        return redirect()->route('admin.burial.requests')->with('error','Failed to message burial assistance requestor');
    }

    public function exportPdf($uuid) {
        $assistanceRequest = BurialAssistanceRequest::findOrFail($uuid);
        $relationships = Relationship::getAllRelationships();
        $barangays = Barangay::getAllBarangays();

        if (!$assistanceRequest) {
            return redirect()->back()->withErrors(['error' => 'Burial Assistance Request not found.']);
        }
        $disk = 'public';
        $folder = 'death_certificates';
        $requestImageFileName = $assistanceRequest->uuid;

        $allFiles = Storage::disk($disk)->files($folder);
        $matchedFiles = collect($allFiles)->filter(function ($filePath) use ($requestImageFileName) {
            return str_starts_with(basename($filePath), $requestImageFileName);
        });

        foreach ($matchedFiles as $filePath) {
            try {
                $encryptedContent = Storage::disk($disk)->get($filePath);
                $decryptedContent = Crypt::decrypt($encryptedContent);

                $requestImages[] = [
                    'filename' => basename($filePath),
                    'content' => $decryptedContent,
                ];
            } catch (\Exception $e) {
                continue;
            }
        }

        $relationships = Relationship::getAllRelationships();
        $barangays = Barangay::getAllBarangays();
        $pdf = Pdf::loadView('admin.printable-request-form', compact('assistanceRequest', 'relationships', 'barangays', 'requestImages'))
            ->setPaper('letter', 'portrait');

        return $pdf->stream("{$assistanceRequest->deceased_firstname} {$assistanceRequest->deceased_lastname}-burial-request-form.pdf");
    }

    public function exportXlsx() {
        try {
            return Excel::download(new BurialAssistanceRequestsExport(), 'burial_assistance_requests.xlsx');
        } catch (\Throwable $e) {
            \Log::error('Export error: ' . $e->getMessage());
            return back()->with('error', 'Export failed: ' . $e->getMessage());
        }
    }
}
