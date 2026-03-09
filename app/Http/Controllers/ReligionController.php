<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReligionRequest;
use App\Models\Religion;
use App\Services\DatatableService;
use App\Services\ReligionService;
use Illuminate\Http\Request;
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
        $data = Religion::getAllReligions()->map(function ($religion) {
            return [
                'id' => $religion->id,
                'name' => $religion->name,
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

    public function edit(Religion $religion)
    {
        $page_title = 'Religion';
        $resource = 'religion';
        $data = $religion;

        return view('cms.edit', compact('page_title', 'data', 'resource'));
    }

    public function update($id, Request $request)
    {
        $religion = Religion::findOrFail($id);
        $religion = $this->religionServices->updateReligion($request->validate([
            'name' => 'required',
            'remarks' => 'nullable',
        ]), $religion);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($religion)
            ->withProperties(['ip' => request()->ip(), 'browser' => request()->header('User-Agent')])
            ->log('Updated the religion: '.$religion->name);

        return redirect()
            ->route('religion.index')
            ->with('success', 'Religion updated successfully.');
    }

    public function destroy(Religion $religion)
    {
        $religion = $this->religionServices->deleteReligion($religion);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($religion)
            ->log('Created the religion: '.$religion->name);

        return redirect()
            ->route('religion.index')
            ->with('success', 'You have successfully deleted a religion!');
    }
}
