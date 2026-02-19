<?php

namespace App\Http\Controllers;

use App\DataTables\CmsDataTable;
use App\Http\Requests\ReligionRequest;
use App\Models\Religion;
use App\Services\ReligionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReligionController extends Controller
{
    protected $religionServices;

    public function __construct(ReligionService $religionServices)
    {
        $this->religionServices = $religionServices;
    }

    public function index()
    {
        $page_title = 'Religion';
        $resource = 'religion';
        $columns = ['id', 'name', 'remarks', 'action'];
        $data = Religion::getAllReligions();

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
        try {
            $religion = Religion::find($id);
            $religion = $this->religionServices->updateReligion($request->validate([
                'name' => 'required',
                'remarks' => 'nullable',
            ]), $religion);
    
            activity()
                ->causedBy(Auth::user())
                ->performedOn($religion)
                ->withProperties(['ip' => request()->ip(), 'browser' => request()->header('User-Agent')])
                ->log('Created the religion: '.$religion->name);
    
            return redirect()
                ->route('religion.index')
                ->with('success', 'Religion updated successfully.');
        } catch (\Throwable $th) {
            return back()->with('error', 'Unable to update religion ' . config('app.env') == 'local' ? $th->getMessage() : '');
        }
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
