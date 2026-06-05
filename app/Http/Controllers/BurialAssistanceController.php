<?php

namespace App\Http\Controllers;

use App\Models\Barangay;
use App\Models\BurialAssistance;
use App\Models\Relationship;
use App\Models\Religion;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\WorkflowStep;
use App\Services\BurialAssistanceService;
use App\Services\DatatableService;
use App\Services\ProcessLogService;
use App\Services\SmsService;
use App\Services\WorkflowStepService;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BurialAssistanceController extends Controller
{
    protected $processLogServices;

    protected $burialAssistanceServices;

    protected $datatableServices;

    protected $workflowStepServices;

    protected $smsServices;

    public function __construct(
        ProcessLogService $processLogService,
        BurialAssistanceService $burialAssistanceService,
        DatatableService $datatableService,
        WorkflowStepService $workflowStepService,
        SmsService $smsService
    ) {
        $this->processLogServices = $processLogService;
        $this->burialAssistanceServices = $burialAssistanceService;
        $this->datatableServices = $datatableService;
        $this->workflowStepServices = $workflowStepService;
        $this->smsServices = $smsService;
    }

    public function index()
    {
        $page_title = 'Burial Assistance Applications';
        $resource = 'burial';

        $personalData = $this->burialAssistanceServices->index(auth()->user()->id);
        $personalDataColumns = $this->datatableServices->getColumns($personalData, ['id', 'status', 'show_route']);

        $cardData = [];
        $allData = [];
        $allDataColumns = [];
        if (auth()->user()->hasRole('staff')) {
            $allData = $this->burialAssistanceServices->index();
            $allDataColumns = $this->datatableServices->getColumns($allData, ['id', 'status', 'show_route']);
            $cardData = [
                [
                    'model' => 'App\Models\BurialAssistance',
                    'label' => 'Total Applications',
                    'scope' => 'Total',
                    'iconName' => 'some-files',
                    'iconPathsCount' => 2,
                ],
                [
                    'model' => 'App\Models\BurialAssistance',
                    'label' => 'Pending',
                    'scope' => 'Pending',
                    'iconName' => 'document',
                    'iconPathsCount' => 2,
                ],
                [
                    'model' => 'App\Models\BurialAssistance',
                    'label' => 'Processing',
                    'scope' => 'Processing',
                    'iconName' => 'file-right',
                    'iconPathsCount' => 2,
                ],
                [
                    'model' => 'App\Models\BurialAssistance',
                    'label' => 'Released',
                    'scope' => 'Released',
                    'iconName' => 'file-added',
                    'iconPathsCount' => 2,
                ],
            ];
        }

        if (request()->expectsJson()) {
            return response()->json([
                'personalData' => $personalData ? $personalData->values() : [],
                'allData' => $allData ? $allData->values() : [],
            ]);
        }

        return view('burial.index', compact(
            'resource',
            'personalData',
            'personalDataColumns',
            'allData',
            'allDataColumns',
            'cardData',
            'page_title',
        ));
    }

    public function show($id)
    {
        try {
            $data = BurialAssistance::findOrFail($id);
            $this->authorize('view', $data);

            $currentClaimant = $data->currentClaimant();
            $originalClaimant = $data->originalClaimant();
            $claimantChange = $data->claimantChanges()->first();
            $newClaimants = [];

            if (! $currentClaimant) {
                abort(404);
            }

            if (! $claimantChange) {
                $currentClaimantUserId = $currentClaimant->client?->user_id;
                $newClaimants = User::whereHas('clients')
                    ->where('id', '!=', $currentClaimantUserId)
                    ->get()
                    ->mapWithKeys(function ($user) {
                        return [$user->id => $user->fullname()];
                    })
                    ->toArray();
            }

            $page_title = $data->originalClaimant()?->client?->tracking_no;
            $page_subtitle = $currentClaimant->fullname()."'s Burial Assistance Application";
            $readonly = ! auth()->user()->hasRole('superadmin') && $data->status == 'released';

            $timeline = $this->processLogServices->timeline($data);

            $totalSteps = WorkflowStep::count();
            $next_step = $this->workflowStepServices->nextStep($data);
            if ($next_step == null) {
                if ($data->status == 'approved') {
                    $current_step = 13;
                } elseif ($data->status == 'released') {
                    $current_step = 13;
                } else {
                    $current_step = 0;
                }
            } else {
                $current_step = $next_step->order_no - 1;
            }

            $progress = $this->workflowStepServices->progress($data, $next_step, $totalSteps);
            $show_certificate = in_array($data->status, ['approved', 'released']);
            $relationships = Relationship::select('id', 'name')->get();

            return view('burial.show', compact(
                'data',
                'currentClaimant',
                'originalClaimant',
                'claimantChange',
                'newClaimants',
                'relationships',
                'progress',
                'timeline',
                'totalSteps',
                'current_step',
                'next_step',
                'show_certificate',
                'page_title',
                'page_subtitle',
                'readonly',
            ));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Unable to find application.'.(app()->hasDebugModeEnabled() ? ' '.$th->getMessage() : ''));
        }
    }

    public function generatePdfReport(Request $request, $startDate, $endDate)
    {
        try {
            $burialAssistance = $this->burialAssistanceServices->reportIndex($startDate, $endDate);

            $deceasedPerBarangay = Barangay::query()
                ->select('id', 'name')
                ->withCount([
                    'beneficiary as beneficiary_count' => function ($query) use ($startDate, $endDate) {
                        $query->whereHas('client.claimant', function ($q) use ($startDate, $endDate) {
                            $q->whereBetween('date_of_death', [$startDate, $endDate]);
                        });
                    },
                ])
                ->get()
                ->mapWithKeys(function ($barangay) {
                    return [$barangay->name => $barangay->beneficiary_count];
                })
                ->toArray();

            $deceasedPerReligion = Religion::query()
                ->select('id', 'name')
                ->withCount([
                    'beneficiary as beneficiary_count' => function ($query) use ($startDate, $endDate) {
                        $query->whereHas('client.claimant', function ($q) use ($startDate, $endDate) {
                            $q->whereBetween('date_of_death', [$startDate, $endDate]);
                        });
                    },
                ])
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
        $assistance = BurialAssistance::findOrFail($id);

        if (! app()->isLocal() && ! in_array($assistance->status, ['released', 'approved'])) {
            return redirect()->back()->with('error', 'Certificate is not yet available.');
        }

        $claimant = $assistance->currentClaimant();

        if (! $claimant) {
            return back()->with('error', 'Claimant not found.');
        }

        $title = Str::title($claimant->first_name).' '.Str::title($claimant->last_name).'\'s Certificate of Eligibility';
        $social_welfare_officer = Str::upper(SystemSetting::first()?->social_welfare_officer);
        $dept_head = Str::upper(SystemSetting::first()?->dept_head);

        $pdf = Pdf::loadView('pdf.certificate-of-eligibility',
            compact(
                'claimant',
                'title',
                'social_welfare_officer',
                'dept_head',
            ))
            ->setPaper('letter', 'portrait');

        return $pdf->stream("certificate-of-eligibility-{$claimant->first_name}-{$claimant->last_name}.pdf");
    }
}
