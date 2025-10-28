<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBurialAssistanceRequest;
use App\Models\Religion;
use App\Services\ProcessLogService;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Crypt;
use Carbon\Carbon;
use Storage;
use Illuminate\Http\Request;
use App\Models\Barangay;
use App\Models\Relationship;
use App\Models\Claimant;
use App\Models\Deceased;
use App\Models\BurialAssistance;
use Str;

class BurialAssistanceController extends Controller
{
    protected $processLogService;

    public function view() {
        $barangays = Barangay::select('id', 'name')->get();
        $relationships = Relationship::select('id', 'name')->get();
        return view('guest.burial-assistance.view', compact(
            'barangays',
            'relationships',
        ));
    }

    public function store(StoreBurialAssistanceRequest $request) {
        try {
            $validated = $request->validated();
            $existingDeceased = Deceased::where(function ($query) use ($validated) {
                $query->where('first_name', $validated['deceased']['first_name']);
                $query->where('middle_name', $validated['deceased']['middle_name']);
                $query->where('last_name', $validated['deceased']['last_name']);
                $query->where('suffix', $validated['deceased']['suffix']);
            })->first();
            if (!$existingDeceased) {
                $validated['deceased']['id'] = Str::uuid();
                $validated['claimant']['id'] = Str::uuid();


                $deceased = Deceased::create($validated['deceased']);
                $claimant = Claimant::create($validated['claimant']);
                $burialAssistance = BurialAssistance::create([
                    'id' => Str::uuid(),
                    'tracking_code' => strtoupper(Str::random(6)),
                    'application_date' => now(),
                    'claimant_id' => $claimant->id,
                    'deceased_id' => $deceased->id,
                    'funeraria' => $validated['funeraria'],
                    'amount' => $validated['amount'],
                    'remarks' => $validated['remarks'],
                ]);
                foreach ($request->file('images', []) as $fieldName => $uploadedFile) {
                    $extension = $uploadedFile->getClientOriginalExtension();
                    $filename = $fieldName . '.' . $extension . '.enc';
                    $path = "burial-assistance/{$burialAssistance->tracking_no}/";
                    Storage::disk('local')->put($path . $filename, Crypt::encrypt(file_get_contents($uploadedFile)));
                }

                activity()
                    ->causedBy(null)
                    ->withProperties(['ip' => request()->ip(), 'browser' => request()->header('User-Agent')])
                    ->log('Burial Assistance application submitted by guest');
        
                return redirect()->route('landing.page')
                    ->with(
                        'alertSuccess', 
                        "Successfully submitted burial assistance application. Please check your SMS messages for the assistance tracking code. You can use the given code to track the progress of the assistance application."
                    );
            } else {
                return redirect()->back()
                ->with('alertInfo', 'A burial assistance has already been submitted for this deceased person.');
            }
        } catch (Exception $e) {
            return redirect()->back()->with('alertError', 'Something went wrong. Please try again.' . $e->getMessage());
        }
    }

    public function track(Request $request) {
        $request->validate([
            'tracking_code' => 'required|string|exists:burial_assistances,tracking_code',
            'mobile_number' => 'required|string',
        ]);

        $burialAssistance = BurialAssistance::where('tracking_code', $request->tracking_code)->first();
        if ($burialAssistance->claimantChanges()->count() > 0) {
            if ($burialAssistance->claimantChanges()->latest()->first()->where('status', 'approved')) {
                $mobileNumber = substr($burialAssistance->claimantChanges()->where('status', 'approved')->latest()->first()->newclaimant->mobile_number, -4);
            } else {
                $mobileNumber = substr($burialAssistance->claimant->mobile_number, -4);
            }
        } else {
            $mobileNumber = substr($burialAssistance->claimant->mobile_number, -4);
        }
        if (!$burialAssistance) {
            return redirect()->back()->with([
                'error'=> 'Burial Assistance Application not found.'
            ]);
        } elseif ($request->mobile_number != $mobileNumber) {
            return redirect()->back()->with([
                'info' => 'Mobile number does not match. Please try again.'
            ]);
        }

        // dd($burialAssistance);

        $ip = request()->ip();
        $browser = request()->header('User-Agent');
        activity()
            ->causedBy(null)
            ->withProperties(['ip' => $ip, 'browser' => $browser])
            ->log('Burial Assistance tracked by guest');

        // return view('guest.burial-assistance.tracker', compact('burialAssistance'));
        return redirect()->route('guest.burial-assistance.track-page', ['code' => $request->tracking_code]);
    }

    public function trackPage($code, ProcessLogService $processLogService) {
        $burialAssistance = BurialAssistance::where('tracking_code', $code)->first();

        $updateAverage = $processLogService->getAvgProcessingTime($burialAssistance)->avg();

        if (!auth()->check()) {
            $burialAssistance->claimant->first_name = Str::mask($burialAssistance->claimant->first_name, '*', 3);
            $burialAssistance->claimant->middle_name = Str::mask($burialAssistance->claimant->middle_name, '*', 3);
            $burialAssistance->claimant->last_name = Str::mask($burialAssistance->claimant->last_name, '*', 3);
            $burialAssistance->claimant->mobile_number = Str::mask($burialAssistance->claimant->mobile_number, '*', 4, 3);
            $burialAssistance->claimant->address = Str::mask($burialAssistance->claimant->address, '*', 3);
        }
        return view('guest.burial-assistance.tracker', compact('burialAssistance', 'updateAverage'));
    }

    // Admin Side
    public function pending() {
        $applications = BurialAssistance::where('status','pending')->get();
        $status = 'pending';
        $badge = 'primary';
        return view('applications.list', compact('applications', 'status', 'badge'));
    }

    public function processing() {
        $applications = BurialAssistance::where('status','processing')->get();
        $status = 'processing';
        $badge = 'info';
        return view('applications.list', compact('applications', 'status', 'badge'));
    }
    
    public function approved() {
        $applications = BurialAssistance::where('status','approved')->get();
        $status = 'approved';
        $badge = 'success';
        return view('applications.list', compact('applications', 'status', 'badge'));
    }

    public function released() {
        $applications = BurialAssistance::where('status','released')->get();
        $status = 'released';
        $badge = 'success';
        return view('applications.list', compact('applications', 'status', 'badge'));
    }

    public function history() {
        $applications = BurialAssistance::select('id', 'deceased_id', 'claimant_id', 'tracking_no', 'funeraria', 'amount', 'application_date', 'status', 'created_at')->get();
        $status = 'All';
        return view('applications.list', compact('applications', 'status'));
    }

    public function manage($id, ProcessLogService $processLogService) {
        $application = BurialAssistance::findOrFail($id);
        $path = "burial-assistance/{$application->tracking_no}";
        $storedFiles = Storage::disk('local')->files($path);
        $files = [];
        $updateAverage = $processLogService->getAvgProcessingTime($application)->avg();

        foreach ($storedFiles as $storedFile) {
            // TODO: Use API to store images
            $encryptedFile = Storage::disk('local')->get($storedFile);
            $decryptedFile = Crypt::decrypt($encryptedFile);
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->buffer($decryptedFile);
            $files[] = [
                'name' => basename($storedFile, '.enc'),
                'path' => $storedFile,
                'content' => $decryptedFile,
                'mime' => $mime,
            ];
        }
        return view('applications.manage', compact('application', 'files', 'updateAverage'));
    }

    public function saveSwa(Request $request, $id) {
        $request->validate([
            'swa' => 'required|string|max:255',
        ]);
        $application = BurialAssistance::findOrFail($id);
        if ($application) {
            $application->swa = $request->swa;
            $application->encoder = auth()->user()->id;
            $application->update();
            return redirect()->back()->with('alertSuccess', 'Successfully updated SWA.');
        } else {
            return redirect()->back()->with('alertInfo', 'Application not found.');
        }
    }

    public function reject($id) {
        $application = BurialAssistance::where('id',$id)->first();
        if (!$application) {
            return back()->with('error','Application not found.');
        }

        $application->status = 'rejected';
        $application->update();
        return redirect()->back()->with('success', 'Successfully rejected burial assistance application.');
    }

    public function toggleReject($id) {
        $application = BurialAssistance::where('id', $id)->first();
        if (!$application) {
            return back()->with('error', 'Application not found.');
        }

        $application->status = $application->status == 'rejected' ? 'pending' : 'rejected';
        $application->update();

        return redirect()->back()->with('success', 'Successfully updated burial assistance application\'s status.');
    }

    public function assignments() {
        $applications = BurialAssistance::select('id', 'tracking_no', 'deceased_id', 'claimant_id', 'application_date', 'status', 'assigned_to')->get();
        return view('superadmin.assignment', compact('applications'));
    }

    public function assign(Request $request, $id) {
        $request->validate([
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $application = BurialAssistance::where('id', $id)->first();
        if (!$application) {
            return back()->with('alertError', 'Application not found.');
        }

        $application->assigned_to = $request->assigned_to;
        $application->update();

        return redirect()->route('superadmin.assignments')->with('alertSuccess', 'Successfully updated assignment.');
    }

    public function generatePdfReport(Request $request, $startDate, $endDate) {
        // try {
            $burialAssistance = BurialAssistance::select(
                'id',
                'tracking_no',
                'application_date',
                'encoder',
                'funeraria',
                'deceased_id',
                'claimant_id',
                'amount',
                'initial_checker',
                'assigned_to',
                'created_at',
            )
            ->with('deceased', 'claimant', 'assignedTo', 'encoder', 'initialChecker')
            ->whereBetween('application_date', [$startDate, $endDate])
            ->get();

            $barangays = Barangay::with(['deceased' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('deceased.date_of_death', [$startDate, $endDate]);
            }])
                ->select('id', 'name')
                ->whereHas('deceased', function($query) use ($startDate, $endDate) {
                    $query->whereBetween('deceased.date_of_death', [$startDate, $endDate]);
                })
                ->get();

            $religions = Religion::with(['deceased' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('deceased.date_of_death', [$startDate, $endDate]);
            }])
                ->select('id', 'name')
                ->with('deceased')
                ->whereHas('deceased', function($query) use ($startDate, $endDate) {
                    $query->whereBetween('deceased.date_of_death', [$startDate, $endDate]);
                })
                ->get();
            
            $charts = $request->input('charts', []);

            $pdf = Pdf::loadView('pdf.burial-assistance', compact(
                'burialAssistance', 
                'barangays', 
                'religions', 
                'charts',
                'startDate',
                'endDate'
            ))
            ->setPaper('letter', 'portrait');

            return $pdf->stream("burial-assistance-report-{$startDate}-to-{$endDate}.pdf");
        // } catch (Exception $e) {
        //     return back()->with('alertError', 'Error generating report: ' . $e->getMessage());
        // }
    }
}
