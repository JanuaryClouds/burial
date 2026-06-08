<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReligionRequest;
use App\Models\Religion;
use App\Services\DatatableService;
use App\Services\ReligionService;
use Illuminate\Support\Facades\Auth;

class ReligionController extends Controller
{
    protected $religionServices;

    protected $datatableServices;

    public function __construct(ReligionService $religionServices, DatatableService $datatableService)
    {
        $this->religionServices = $religionServices;
        $this->datatableServices = $datatableService;
    }

    public function index()
    {
        $page_title = 'Religion';
        $resource = 'religion';
        $data = Religion::withTrashed()
            ->get()
            ->map(function ($religion) {
                return [
                    'id' => $religion->id,
                    'name' => $religion->name.($religion->trashed() ? ' (disabled)' : ''),
                    'remarks' => $religion->remarks,
                    'show_route' => route('religion.edit', $religion->id),
                ];
            });
        $columns = $this->datatableServices->getColumns($data, ['id', 'show_route']);

        if (request()->expectsJson()) {
            return response()->json([
                'data' => $data->values(),
            ]);
        }

        return view('cms.index', compact(
            'page_title',
            'resource',
            'columns',
            'data',
        ));
    }

    public function store(ReligionRequest $request)
    {
        $religion = $this->religionServices->storeReligion($request->validated());

        activity()
            ->causedBy(Auth::user())
            ->performedOn($religion)
            ->log('Created a new religion: '.$religion->name);

        return redirect()
            ->route('religion.index')
            ->with('success', 'You have successfully created a religion!');
    }

    public function edit($id)
    {
        $page_title = 'Religion';
        $resource = 'religion';
        $data = Religion::withTrashed()->findOrFail($id);

        return view('cms.edit', compact('page_title', 'data', 'resource'));
    }

    public function update($id, ReligionRequest $request)
    {
        $religion = Religion::withTrashed()->findOrFail($id);
        $this->religionServices->updateReligion($request->validated(), $religion);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($religion)
            ->withProperties(['ip' => request()->ip(), 'browser' => request()->header('User-Agent')])
            ->log('Updated the religion: '.$religion->name);

        return redirect()
            ->route('religion.index')
            ->with('success', 'Religion updated successfully.');
    }

    public function destroy($id)
    {
        $religion = Religion::withTrashed()->findOrFail($id);
        activity()
            ->causedBy(Auth::user())
            ->performedOn($religion)
            ->log('Deleted the religion: '.$religion->name);

        $this->religionServices->deleteReligion($religion);

        return redirect()
            ->route('religion.index')
            ->with('success', 'Religion soft deleted successfully');
    }

    public function restore($id)
    {
        $religion = Religion::withTrashed()->findOrFail($id);
        $this->religionServices->restoreReligion($religion);

        activity()
            ->performedOn($religion)
            ->causedBy(Auth::user())
            ->withProperties(['ip' => request()->ip(), 'browser' => request()->header('User-Agent')])
            ->log('Restored the religion: '.$religion->name);

        return redirect()
            ->route('religion.index')
            ->with('success', 'Religion restored successfully.');
    }
}
