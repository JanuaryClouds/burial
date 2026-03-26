<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckTrackingCodeRequest;
use App\Models\BurialAssistance;
use App\Models\TrackingCode;
use App\Services\ProcessLogService;
use App\Services\TrackingCodeService;
use App\Services\WorkflowStepService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TrackingCodeController extends Controller
{
    protected $trackingCodeServices;

    protected $processLogServices;

    protected $workflowStepServices;

    public function __construct(
        TrackingCodeService $trackingCodeService,
        ProcessLogService $processLogService,
        WorkflowStepService $workflowStepService
    ) {
        $this->trackingCodeServices = $trackingCodeService;
        $this->processLogServices = $processLogService;
        $this->workflowStepServices = $workflowStepService;
    }

    /**
     * Summary of store
     *
     * @param  Model  $assistance  The assistance model instance
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Model $assistance)
    {
        try {
            $this->trackingCodeServices->create([
                'trackable' => $assistance,
            ]);

            return redirect()->back()->with('success', 'Tracking Code created successfully');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Failed to create Tracking Code');
        }
    }

    public function match(CheckTrackingCodeRequest $request)
    {
        try {
            $validated = $request->validated();
            $match = $this->trackingCodeServices->match($validated['code'], $validated['tracking_no']);
            if ($match) {
                $parsed_code = Str::replace('-', '', $validated['code']);

                return redirect()->route('tracker.show')
                    ->with('code', TrackingCode::where('code', $parsed_code)->firstOrFail()->uuid);
            } else {
                return redirect()->route('landing.page')->with('info', 'Invalid Tracking Code or Tracking Number'.(app()->hasDebugModeEnabled() ? ' : '.$match : ''));
            }
        } catch (\Throwable $th) {
            return redirect()->route('landing.page')->with('error', 'Failed to match Tracking Code'.(app()->hasDebugModeEnabled() ? ' : '.$th->getMessage() : ''));
        }
    }

    public function show()
    {
        try {
            if (! session()->has('code')) {
                return redirect()->route('landing.page');
            }

            $tracking_code = TrackingCode::with('trackable')->findOrFail(session('code'));
            session()->forget('code');
            $data = $tracking_code->trackable;

            if (get_class($data) === BurialAssistance::class) {
                $timeline = $this->processLogServices->timeline($data);
            }

            return view('tracker.show', [
                'page_title' => $data->trackingNumber() ? $data->trackingNumber().' Progress' : 'Track Progress',
                'data' => $data,
                'timeline' => $timeline ?? [],
                'progress' => $data instanceof BurialAssistance ? $this->workflowStepServices->progress($data) : null,
                'show_tracker_code' => auth()->user()->can('view', $data->tracker),
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404, 'Tracker not found');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Failed to show tracker'.(app()->hasDebugModeEnabled() ? ' : '.$th->getMessage() : ''));
        }
    }
}
