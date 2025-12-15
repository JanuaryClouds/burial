<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBurialAssistanceRequest;
use App\Models\Barangay;
use App\Models\BurialAssistance;
use App\Models\Claimant;
use App\Models\Deceased;
use App\Models\Relationship;
use App\Models\Religion;
use App\Services\BurialAssistanceService;
use App\Services\ProcessLogService;
use Barryvdh\DomPDF\Facade\Pdf;
use Crypt;
use Exception;
use Illuminate\Http\Request;
use Storage;
use Str;

class BurialAssistanceController extends Controller
{
    protected $processLogService;

    protected $burialAssistanceService;

    public function __construct(ProcessLogService $processLogService, BurialAssistanceService $burialAssistanceService)
    {
        $this->processLogService = $processLogService;
        $this->burialAssistanceService = $burialAssistanceService;
    }

    // ! Unused
    public function view()
    {
        $barangays = Barangay::select('id', 'name')->get();
        $relationships = Relationship::select('id', 'name')->get();

        return view('burial.view', compact(
            'barangays',
            'relationships',
        ));
    }

    // ! Unused
    public function store(StoreBurialAssistanceRequest $request)
    {
        try {
            $validated = $request->validated();
            $existingDeceased = Deceased::where(function ($query) use ($validated) {
                $query->where('first_name', $validated['deceased']['first_name']);
                $query->where('middle_name', $validated['deceased']['middle_name']);
                $query->where('last_name', $validated['deceased']['last_name']);
                $query->where('suffix', $validated['deceased']['suffix']);
            })->first();
            if (! $existingDeceased) {
                $validated['deceased']['id'] = Str::uuid();
                $validated['claimant']['id'] = Str::uuid();

                $deceased = Deceased::create($validated['deceased']);
                $claimant = Claimant::create($validated['claimant']);
                $burialAssistance = BurialAssistance::create([
                    'id' => Str::uuid(),
                    'tracking_code' => strtoupper(Str::random(6)),
                    'application_date' => now(),
                    'funeraria' => $validated['funeraria'],
                    'amount' => $validated['amount'],
                    'remarks' => $validated['remarks'],
                ]);
                foreach ($request->file('images', []) as $fieldName => $uploadedFile) {
                    $extension = $uploadedFile->getClientOriginalExtension();
                    $filename = $fieldName.'.'.$extension.'.enc';
                    $path = "burial-assistance/{$burialAssistance->tracking_no}/";
                    Storage::disk('local')->put($path.$filename, Crypt::encrypt(file_get_contents($uploadedFile)));
                }

                activity()
                    ->causedBy(null)
                    ->withProperties(['ip' => request()->ip(), 'browser' => request()->header('User-Agent')])
                    ->log('Burial Assistance application submitted by guest');

                return redirect()->route('landing.page')
                    ->with(
                        'success',
                        'Successfully submitted burial assistance application. Please check your SMS messages for the assistance tracking code. You can use the given code to track the progress of the assistance application.'
                    );
            } else {
                return redirect()->back()
                    ->with('info', 'A burial assistance has already been submitted for this deceased person.');
            }
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong. Please try again.'.$e->getMessage());
        }
    }

    // public function track(Request $request) {
    //     $request->validate([
    //         'tracking_code' => 'required|string|exists:burial_assistances,tracking_code',
    //         'mobile_number' => 'required|string',
    //     ]);

    //     $burialAssistance = BurialAssistance::where('tracking_code', $request->tracking_code)->first();
    //     if ($burialAssistance->claimantChanges()->count() > 0) {
    //         if ($burialAssistance->claimantChanges()->latest()->first()->where('status', 'approved')) {
    //             $mobileNumber = substr($burialAssistance->claimantChanges()->where('status', 'approved')->latest()->first()->newclaimant->mobile_number, -4);
    //         } else {
    //             $mobileNumber = substr($burialAssistance->claimant->mobile_number, -4);
    //         }
    //     } else {
    //         $mobileNumber = substr($burialAssistance->claimant->mobile_number, -4);
    //     }
    //     if (!$burialAssistance) {
    //         return redirect()->back()->with([
    //             'error'=> 'Burial Assistance Application not found.'
    //         ]);
    //     } elseif ($request->mobile_number != $mobileNumber) {
    //         return redirect()->back()->with([
    //             'info' => 'Mobile number does not match. Please try again.'
    //         ]);
    //     }

    //     // dd($burialAssistance);

    //     $ip = request()->ip();
    //     $browser = request()->header('User-Agent');
    //     activity()
    //         ->causedBy(null)
    //         ->withProperties(['ip' => $ip, 'browser' => $browser])
    //         ->log('Burial Assistance tracked by guest');

    //     // return view('guest.burial-assistance.tracker', compact('burialAssistance'));
    //     return redirect()->route('guest.burial-assistance.track-page', ['code' => $request->tracking_code]);
    // }

    public function tracker($uuid, ProcessLogService $processLogService)
    {
        $burialAssistance = BurialAssistance::where('id', $uuid)->first();

        $updateAverage = $processLogService->getAvgProcessingTime($burialAssistance)->avg();

        if (! auth()->check()) {
            $burialAssistance->claimant->first_name = Str::mask($burialAssistance->claimant->first_name, '*', 3);
            $burialAssistance->claimant->middle_name = Str::mask($burialAssistance->claimant->middle_name, '*', 3);
            $burialAssistance->claimant->last_name = Str::mask($burialAssistance->claimant->last_name, '*', 3);
            $burialAssistance->claimant->mobile_number = Str::mask($burialAssistance->claimant->mobile_number, '*', 4, 3);
            $burialAssistance->claimant->address = Str::mask($burialAssistance->claimant->address, '*', 3);
        }

        return view('burial.tracker', compact('burialAssistance', 'updateAverage'));
    }

    public function index($status = null)
    {
        $page_title = 'Burial Assistance Applications';
        $applications = BurialAssistance::select('id', 'tracking_no', 'funeraria', 'amount', 'application_date', 'status', 'assigned_to', 'created_at')
            ->where(function ($query) use ($status) {
                try {
                    if ($status == 'all') {
                        return $query;
                    } else {
                        return $query->where('status', $status);
                    }
                } catch (Exception $e) {
                    return redirect()->back()->with('error', $e->getMessage());
                }
            })
            ->orderBy('created_at', 'desc')
            ->get();

        if ($applications->isNotEmpty()) {
            $statusOptions = array_unique($applications->pluck('status')->toArray());
        } else {
            $applications = [];
            $statusOptions = [];
        }
        $barangays = Barangay::select('id', 'name')->get();

        return view('burial.index', compact('applications', 'status', 'barangays', 'statusOptions', 'page_title'));
    }

    public function show($id, ProcessLogService $processLogService)
    {
        $application = BurialAssistance::findOrFail($id);
        $client = $application->claimant->client;
        $page_title = $client->first_name.' '.$client->last_name.'\'s Burial Assistance Application';
        $readonly = ! auth()->user()->can('manage-content');
        $path = "clients/{$client->tracking_no}";
        $storedFiles = Storage::disk('local')->files($path);
        $files = collect($storedFiles)->map(function ($file) {
            return [
                'name' => basename($file),
                'path' => $file,
            ];
        });
        $updateAverage = $processLogService->getAvgProcessingTime($application)->avg();

        return view('burial.manage', compact('application', 'files', 'updateAverage', 'page_title', 'readonly'));
    }

    public function update(StoreBurialAssistanceRequest $request, $id)
    {
        try {
            $burialAssistance = BurialAssistance::where('id', $id)->with('claimant', 'deceased')->first();
            $burialAssistance = $this->burialAssistanceService->update($request->validated(), $burialAssistance);

            return redirect()->back()->with('success', 'Successfully updated application.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'An error occurred.'.$e->getMessage());
        }

    }

    public function toggleReject(Request $request, $id)
    {
        try {
            $application = BurialAssistance::where('id', $id)->first();
            if (! $application) {
                return back()->with('error', 'Application not found.');
            }

            if (! empty($application->rejection)) {
                $application->rejection()->delete();
            }

            if ($application->status != 'rejected') {
                $validated = $request->validate([
                    'reason' => 'required|string|max:255',
                ]);

                $application->rejection()->create([
                    'id' => Str::uuid(),
                    'reason' => $validated['reason'],
                    'burial_assistance_id' => $application->id,
                ]);

                // TODO: Send notification via SMS
            }

            if ($application->processLogs()->count() > 0) {
                $application->status = $application->status == 'rejected' ? 'processing' : 'rejected';
                $application->update();
            } else {
                $application->status = $application->status == 'rejected' ? 'pending' : 'rejected';
                $application->update();
            }

            return redirect()->back()->with('success', 'Successfully updated burial assistance application status.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Unable to update burial assistance application status. '.$e->getMessage());
        }
    }

    public function assignments()
    {
        $page_title = 'Burial Assistance Assignments';
        $applications = BurialAssistance::select('id', 'tracking_no', 'application_date', 'status', 'assigned_to')->get();

        return view('superadmin.assignment', compact('applications', 'page_title'));
    }

    public function assign(Request $request, $id)
    {
        $request->validate([
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $application = BurialAssistance::where('id', $id)->first();
        if (! $application) {
            return back()->with('error', 'Application not found.');
        }

        $application->assigned_to = $request->assigned_to;
        $application->update();

        return redirect()->back()->with('success', 'Successfully updated assignment.');
    }

    public function generatePdfReport(Request $request, $startDate, $endDate)
    {
        try {
            $burialAssistance = BurialAssistance::select(
                'id',
                'tracking_no',
                'application_date',
                'encoder',
                'funeraria',
                'amount',
                'initial_checker',
                'assigned_to',
                'created_at',
            )
                ->with('deceased', 'claimant', 'assignedTo', 'encoder', 'initialChecker')
                ->whereBetween('application_date', [$startDate, $endDate])
                ->get();

            $barangays = Barangay::with(['deceased' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('deceased.date_of_death', [$startDate, $endDate]);
            }])
                ->select('id', 'name')
                ->whereHas('deceased', function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('deceased.date_of_death', [$startDate, $endDate]);
                })
                ->get();

            $religions = Religion::with(['deceased' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('deceased.date_of_death', [$startDate, $endDate]);
            }])
                ->select('id', 'name')
                ->with('deceased')
                ->whereHas('deceased', function ($query) use ($startDate, $endDate) {
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
        } catch (Exception $e) {
            return back()->with('error', 'Error generating report: '.$e->getMessage());
        }
    }

    public function certificate($id)
    {
        $assistance = BurialAssistance::find($id);
        $claimant = $assistance->claimant;
        $title = Str::title($claimant->first_name).' '.Str::title($claimant->last_name).'\'s Certificate of Eligibility';

        $pdf = Pdf::loadView('pdf.certificate-of-eligibility',
            compact('claimant', 'title'))
            ->setPaper('letter', 'portrait');

        return $pdf->stream("certificate-of-eligibility-{$claimant->first_name}-{$claimant->last_name}.pdf");
    }
}
