<?php

namespace App\Http\Controllers;

use App\Exports\BurialAssistanceRequestsExport;
use App\Http\Requests\BurialAssistanceReqRequest;
use App\Mail\VerifyAssistanceRequestMail;
use App\Models\Barangay;
use App\Models\BurialAssistanceRequest;
use App\Models\BurialService;
use App\Models\Relationship;
use App\Services\BurialAssistanceRequestService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class BurialAssistanceRequestController extends Controller
{
    protected $burialAssistanceRequestService;

    public function __construct(BurialAssistanceRequestService $burialAssistanceRequestService)
    {
        $this->burialAssistanceRequestService = $burialAssistanceRequestService;
    }

    public function tempStore(BurialAssistanceReqRequest $request)
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

        $request->validate([
            'images' => 'required|array|min:1|max:2',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $data['uuid'] = Str::uuid()->toString();

        $tempPaths = [];
        foreach ($request->file('images', []) as $index => $file) {
            $extension = $file->getClientOriginalExtension();
            $filename = $data['uuid'].'-'.$index.'.'.$extension;

            if (Storage::disk('public')->exists('tmp/'.$filename)) {
                Storage::disk('public')->delete('tmp/'.$filename);
            }

            Storage::putFileAs('public/tmp/', $file, $filename);
            $tempPaths[] = $filename;
        }
        // dd($tempPaths);

        return redirect()->route('guest.request.verify.view')->with(
            session([
                'data' => $data,
                'temp_images' => $tempPaths,
            ])
        );
    }

    // ! Not working, no data is loaded in the form when pressing the back button
    public function backToForm()
    {
        $data = session('data', []);
        $temp_images = session('temp_images', []);

        return redirect()->route('guest.burial.request')->with(
            session([
                'data' => $data,
                'temp_images' => $temp_images,
            ])
        );
    }

    public function toVerify()
    {
        $verificationCode = rand(100000, 999999);
        $data = session('data', []);
        $email = session('data')['representative_email'] ?? null;
        // $temp_images = session('temp_images', []);

        \Mail::to($email)->send(new VerifyAssistanceRequestMail(
            $verificationCode
        ));

        session([
            'verification_code' => $verificationCode,
            'data' => $data,
            // 'temp_images' => $temp_images
        ]);

        return view('guest.verification');
    }

    public function store(Request $request)
    {
        $enteredCode = $request->verification_code;
        $storedCode = strval(session('verification_code'));

        if ($enteredCode !== $storedCode) {
            return back()->with('error', 'Invalid verification code.');
        }

        $data = session('data');
        $tempImages = session('temp_images', []);
        $serviceRequest = $this->burialAssistanceRequestService->store($data);

        foreach ($tempImages as $tempPath) {
            $contents = Storage::get('public/tmp/'.$tempPath);
            $encryptedContents = Crypt::encrypt($contents);
            Storage::put('public/death_certificates/'.$tempPath, $encryptedContents);
            Storage::delete('public/tmp/'.$tempPath);
        }

        $ip = request()->ip();
        $browser = request()->header('User-Agent');
        activity()
            ->causedBy(null)
            ->withProperties(['ip' => $ip, 'browser' => $browser])
            ->log('Burial Assistance Request submitted by guest');

        return redirect()->route('guest.request.submit.success')->with('success', 'Burial Assistance Request created successfully.')->with('service', $serviceRequest->uuid);
    }

    public function track(Request $request)
    {
        $request->validate([
            'uuid' => 'required|uuid|exists:burial_assistance_requests,uuid',
        ]);

        $serviceRequest = BurialAssistanceRequest::where('uuid', $request->uuid)->first();
        if (! $serviceRequest) {
            return redirect()->back()->withErrors(['error' => 'Burial Assistance Request not found.']);
        }

        if (! auth()->check()) {
            $serviceRequest->deceased_lastname = Str::mask($serviceRequest->deceased_lastname, '*', 3);
            $serviceRequest->deceased_firstname = Str::mask($serviceRequest->deceased_firstname, '*', 3);
            $serviceRequest->representative = Str::mask($serviceRequest->representative, '*', 3);
            $serviceRequest->representative_phone = Str::mask($serviceRequest->representative_phone, '*', 3);
            $serviceRequest->representative_email = Str::mask($serviceRequest->representative_email, '*', 3) ?? '';
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

    public function index()
    {
        return view('admin.burial-requests');
    }

    public function view($uuid)
    {
        $serviceRequest = BurialAssistanceRequest::where('uuid', $uuid)->first();
        $existingService = BurialService::where(function ($query) use ($serviceRequest) {
            $query
                ->where('deceased_lastname', $serviceRequest->deceased_lastname)
                ->where('deceased_firstname', $serviceRequest->deceased_firstname)
                ->where('burial_address', $serviceRequest->burial_address)
                ->where('barangay_id', $serviceRequest->barangay_id);
        })->exists();

        if (! $serviceRequest) {
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
            'relationships' => $relationships,
            'barangays' => $barangays,
            'requestImages' => $requestImages,
            'existingService' => $existingService,
        ]);
    }

    public function updateStatus(Request $request, $uuid)
    {
        $serviceRequest = BurialAssistanceRequest::where('uuid', $uuid)->first();
        if (! $serviceRequest) {
            return redirect()->back()->withErrors(['error' => 'Burial Assistance Request not found.']);
        }

        $serviceRequest->status = $request->status;
        $serviceRequest->update();

        return redirect()->route('admin.burial.requests')->with('success', 'Status updated successfully.');
    }

    public function contact($uuid)
    {
        $success = true; // placeholder

        if ($success) {
            return redirect()->route('admin.burial.requests')->with('success', 'Successfully messaged burial assistance request.');
        }

        return redirect()->route('admin.burial.requests')->with('error', 'Failed to message burial assistance requestor');
    }

    public function exportPdf($uuid)
    {
        $assistanceRequest = BurialAssistanceRequest::findOrFail($uuid);
        $relationships = Relationship::getAllRelationships();
        $barangays = Barangay::getAllBarangays();

        if (! $assistanceRequest) {
            return redirect()->back()->withErrors(['error' => 'Burial Assistance Request not found.']);
        }
        $disk = 'public';
        $folder = 'death_certificates';
        $requestImageFileName = $assistanceRequest->uuid;

        $allFiles = Storage::disk($disk)->files($folder);
        $matchedFiles = collect($allFiles)->filter(function ($filePath) use ($requestImageFileName) {
            return str_starts_with(basename($filePath), $requestImageFileName);
        });

        $requestImages = [];
        foreach ($matchedFiles as $filePath) {
            try {
                $encryptedContent = Storage::disk($disk)->get($filePath);
                $decryptedContent = Crypt::decrypt($encryptedContent);

                $requestImages[] = [
                    'filename' => basename($filePath),
                    'content' => $decryptedContent,
                ];
            } catch (\Exception $e) {
                $requestImaes[] = [];

                continue;
            }
        }

        $relationships = Relationship::getAllRelationships();
        $barangays = Barangay::getAllBarangays();
        $pdf = Pdf::loadView('admin.printable-request-form', compact('assistanceRequest', 'relationships', 'barangays', 'requestImages'))
            ->setPaper('letter', 'portrait');

        return $pdf->stream("{$assistanceRequest->deceased_firstname} {$assistanceRequest->deceased_lastname}-burial-request-form.pdf");
    }

    public function exportXlsx()
    {
        try {
            return Excel::download(new BurialAssistanceRequestsExport, 'burial_assistance_requests.xlsx');
        } catch (\Throwable $e) {
            \Log::error('Export error: '.$e->getMessage());

            return back()->with('error', 'Export failed: '.$e->getMessage());
        }
    }
}
