<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBurialAssistanceRequest;
use App\Models\Barangay;
use App\Models\BurialAssistance;
use App\Models\Religion;
use App\Models\SystemSetting;
use App\Services\BurialAssistanceService;
use App\Services\DatatableService;
use App\Services\ProcessLogService;
use App\Services\WorkflowStepService;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Str;

class BurialAssistanceController extends Controller
{
    protected $processLogServices;

    protected $burialAssistanceServices;

    protected $datatableServices;

    protected $workflowStepServices;

    public function __construct(
        ProcessLogService $processLogService,
        BurialAssistanceService $burialAssistanceService,
        DatatableService $datatableService,
        WorkflowStepService $workflowStepService
    ) {
        $this->processLogServices = $processLogService;
        $this->burialAssistanceServices = $burialAssistanceService;
        $this->datatableServices = $datatableService;
        $this->workflowStepServices = $workflowStepService;
    }

    public function index()
    {
        $page_title = 'Burial Assistance Applications';
        $resource = 'burial';

        if (auth()->user()->roles()->count() == 0) {
            $user_id = auth()->user()->id;
        }
        
        $data = $this->burialAssistanceServices->index($user_id ?? null);
        $columns = $this->datatableServices->getColumns($data, ['id', 'status', 'show_route']);

        if (request()->expectsJson()) {
            return response()->json([
                'data' => $data->values(),
            ]);
        }

        return view('burial.index', compact([
            'resource',
            'data',
            'columns',
            'page_title',
        ]));
    }

    public function show($id)
    {
        try {
            $data = BurialAssistance::findOrFail($id);
            $client = $data->originalClaimant()->client;
            if (! $client) {
                throw new Exception('Client not found');
            }
            $page_title = 'Manage '.$client->tracking_no;
            $page_subtitle = $client->fullname()."'s Burial Assistance Application";
            $readonly = auth()->user()->cannot('manage-content') && $data->status != 'released';

            $timeline = $this->processLogServices->timeline($data);
            if ($data->claimantChanges()->first() && $data->claimantChanges()->first()->status == 'approved') {
                $page_subtitle = $data->claimantChanges()->first()->newClaimant->fullname()."'s Burial Assistance Application";
            }

            $next_step = $this->workflowStepServices->nextStep($data);
            $progress = $this->workflowStepServices->progress($data);
            $show_certificate = $next_step == null && $data->status == 'approved';

            return view('burial.show', compact([
                'data',
                'progress',
                'timeline',
                'next_step',
                'show_certificate',
                'page_title',
                'page_subtitle',
                'readonly',
            ]));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Unable to find application.'.(app()->hasDebugModeEnabled() ? ' '.$th->getMessage() : ''));
        }
    }

    public function update(StoreBurialAssistanceRequest $request, $id)
    {
        $burialAssistance = BurialAssistance::where('id', $id)->with('claimant', 'deceased')->first();
        $burialAssistance = $this->burialAssistanceServices->update($request->validated(), $burialAssistance);

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
            $burialAssistance = $this->burialAssistanceServices->reportIndex($startDate, $endDate);

            $deceasedPerBarangay = Barangay::query()
                ->select('id', 'name')
                ->withCount([
                    'beneficiary as beneficiary_count' => function ($query) use ($startDate, $endDate) {
                        $query->whereBetween('date_of_death', [$startDate, $endDate]);
                    },
                ])
                ->whereHas('beneficiary', function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('date_of_death', [$startDate, $endDate]);
                })
                ->get()
                ->mapWithKeys(function ($barangay) {
                    return [$barangay->name => $barangay->beneficiary_count];
                })
                ->toArray();

            $deceasedPerReligion = Religion::query()
                ->select('id', 'name')
                ->withCount([
                    'beneficiary as beneficiary_count' => function ($query) use ($startDate, $endDate) {
                        $query->whereBetween('date_of_death', [$startDate, $endDate]);
                    },
                ])
                ->whereHas('beneficiary', function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('date_of_death', [$startDate, $endDate]);
                })
                ->get()
                ->mapWithKeys(function ($religion) {
                    return [$religion->name => $religion->beneficiary_count];
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

        if (! $assistance) {
            abort(404);
        }

        if ($assistance->status != 'released') {
            abort(404);
        }

        $claimant = $assistance->claimant;
        $title = Str::title($claimant->first_name).' '.Str::title($claimant->last_name).'\'s Certificate of Eligibility';
        $social_welfare_officer = Str::upper(Str::replace('_', ' ', SystemSetting::first()?->social_welfare_officer));
        $dept_head = Str::upper(Str::replace('_', ' ', SystemSetting::first()?->dept_head));

        $pdf = Pdf::loadView('pdf.certificate-of-eligibility',
            compact([
                'claimant', 
                'title',
                'social_welfare_officer',
                'dept_head',
            ]))
            ->setPaper('letter', 'portrait');

        return $pdf->stream("certificate-of-eligibility-{$claimant->first_name}-{$claimant->last_name}.pdf");
    }
}
