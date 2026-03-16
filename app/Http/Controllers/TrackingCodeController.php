<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckTrackingCodeRequest;
use App\Models\TrackingCode;
use App\Services\TrackingCodeService;
use Illuminate\Database\Eloquent\Model;

class TrackingCodeController extends Controller
{
    protected $trackingCodeServices;

    public function __construct(TrackingCodeService $trackingCodeService)
    {
        $this->trackingCodeServices = $trackingCodeService;
    }

    /**
     * Summary of store
     *
     * @param Model $assistance The assistance model instance
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
                return redirect()->route('tracker.show', ['uuid' => TrackingCode::where('code', $validated['code'])->firstOrFail()->uuid]);
            } else {
                return redirect()->back()->with('info', 'Invalid Tracking Code or Tracking Number');
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Failed to match Tracking Code');
        }
    }

    public function show(string $uuid)
    {
        try {
            $tracking_code = TrackingCode::findOrFail($uuid);
            $data = $tracking_code->trackable;

            return view('tracker.show', [
                'page_title' => $data->trackingNumber() . ' Progress' ?? 'Track Progress',
                'data' => $data,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404, 'Tracker not found');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Failed to show tracker' . app()->isLocal() ? ' : ' . $th->getMessage() : '');
        }
    }
}
