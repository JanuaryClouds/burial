<?php

namespace App\Http\Controllers;

use App\Http\Requests\ModeOfAssistanceRequest;
use App\Models\ModeOfAssistance;
use App\Services\ModeOfAssistanceService;
use Illuminate\Support\Facades\Auth;

class ModeOfAssistanceController extends Controller
{
    public function __construct(
        protected ModeOfAssistanceService $modeOfAssistanceServices
    ) {}

    public function index()
    {
        $page_title = 'Mode of assistance';
        $resource = 'moa';
        $columns = ['name', 'remarks'];
        $data = ModeOfAssistance::getAllMoas()
            ->map(function ($moa) {
                return [
                    'id' => $moa->id,
                    'name' => $moa->name,
                    'remarks' => $moa->remarks,
                    'show_route' => route('moa.edit', $moa->id),
                ];
            });

        if (request()->expectsJson()) {
            return response()->json([
                'data' => $data->values(),
            ]);
        }

        return view('cms.index', compact(
            'page_title',
            'resource',
            'columns',
            'data'
        ));
    }

    public function store(ModeOfAssistanceRequest $request)
    {
        $moa = $this->modeOfAssistanceServices->storeModeOfAssistance($request->validated());

        activity()
            ->performedOn($moa)
            ->causedBy(Auth::user())
            ->log('Created a new assistance: '.$moa->name);

        return redirect()
            ->route('moa.index')
            ->with('success', 'Mode of assistance created successfully.');
    }

    public function update(ModeOfAssistanceRequest $request, ModeOfAssistance $moa)
    {
        $moa = $this->modeOfAssistanceServices->updateModeOfAssistance($request->validated(), $moa);

        activity()
            ->causedBy(Auth::user())
            ->withProperties(['ip' => request()->ip()])
            ->log('Updated Mode of Assistnace');

        return redirect()
            ->route('moa.index')
            ->with('success', 'Mode of assistance updated successfully.');
    }

    public function destroy(ModeOfAssistance $moa)
    {
        $moa = $this->modeOfAssistanceServices->deleteModeOfAssistance($moa);

        activity()
            ->causedBy(Auth::user())
            ->withProperties(['ip' => request()->ip()])
            ->log('Deleted Mode of Assistnace');

        return redirect()
            ->route('moa.index')
            ->with('success', 'Mode of assistance deleted successfully.');
    }
}
