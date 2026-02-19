<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBurialAssistanceRequest;
use App\Models\Barangay;
use App\Models\BurialAssistance;
use App\Models\Religion;
use App\Services\BurialAssistanceService;
use App\Services\ProcessLogService;
use Barryvdh\DomPDF\Facade\Pdf;
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
        $resource = 'burial';
        $applications = BurialAssistance::select('id', 'tracking_no', 'funeraria', 'amount', 'application_date', 'status', 'assigned_to', 'created_at')
            ->where(function ($query) use ($status) {
                try {
                    if ($status == 'all') {
                        return $query->orderBy('created_at', 'desc');
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

        return view('burial.index', compact('resource', 'applications', 'status', 'barangays', 'statusOptions', 'page_title'));
    }

    public function show($id, ProcessLogService $processLogService)
    {
        try {
            // code...
            $application = BurialAssistance::findOrFail($id);
            $client = $application->claimant->client;
            $page_title = $client->first_name.' '.$client->last_name.'\'s Burial Assistance Application';
            $readonly = auth()->user()->cannot('manage-content') && $application->status != 'released';
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
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Unable to find application.');
        }

    }

    public function update(StoreBurialAssistanceRequest $request, $id)
    {
        $burialAssistance = BurialAssistance::where('id', $id)->with('claimant', 'deceased')->first();
        $burialAssistance = $this->burialAssistanceService->update($request->validated(), $burialAssistance);

        return redirect()->back()->with('success', 'Successfully updated application.');

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

                // TODO Send notification via SMS
                // Unavialable
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

            $deceasedPerBarangay = Barangay::query()
                ->select('id', 'name')
                ->withCount([
                    'deceased as deceased_count' => function ($query) use ($startDate, $endDate) {
                        $query->whereBetween('date_of_death', [$startDate, $endDate]);
                    },
                ])
                ->whereHas('deceased', function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('date_of_death', [$startDate, $endDate]);
                })
                ->get()
                ->mapWithKeys(function ($barangay) {
                    return [$barangay->name => $barangay->deceased_count];
                })
                ->toArray();

            $deceasedPerReligion = Religion::query()
                ->select('id', 'name')
                ->withCount([
                    'deceased as deceased_count' => function ($query) use ($startDate, $endDate) {
                        $query->whereBetween('date_of_death', [$startDate, $endDate]);
                    },
                ])
                ->whereHas('deceased', function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('date_of_death', [$startDate, $endDate]);
                })
                ->get()
                ->mapWithKeys(function ($religion) {
                    return [$religion->name => $religion->deceased_count];
                })
                ->toArray();

            $charts = $request->input('charts', []);

            $pdf = Pdf::loadView('pdf.burial-assistance', [
                'burialAssistance' => $burialAssistance,
                'deceasedPerReligion' => $deceasedPerReligion,
                'deceasedPerBarangay' => $deceasedPerBarangay,
                'charts' => $charts,
                'startDate' => $startDate,
                'endDate' => $endDate,
            ])
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
