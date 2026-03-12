<?php

namespace App\Http\Controllers;

use App\Http\Requests\SexRequest;
use App\Models\Sex;
use App\Services\SexService;
use Illuminate\Support\Facades\Auth;

class SexController extends Controller
{
    protected $sexServices;

    public function __construct(SexService $sexServices)
    {
        $this->sexServices = $sexServices;
    }

    public function index()
    {
        $page_title = 'Sex';
        $resource = 'sex';
        $columns = ['name', 'remarks'];
        $data = Sex::getAllSexes()->map(function ($sex) {
            return [
                'id' => $sex->id,
                'name' => $sex->name,
                'remarks' => $sex->remarks,
                'show_route' => route('sex.edit', $sex->id),
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

    public function store(SexRequest $request)
    {
        $gender = $this->sexServices->storeSex($request->validated());
        activity()
            ->causedBy(Auth::user())
            ->performedOn($gender)
            ->log('Created a new gender: '.$gender->name);

        return redirect()
            ->route(Auth::user()->getRoleNames()->first().'.sex.index')
            ->with('success', 'You have successfully created a gender!');
    }

    public function update(SexRequest $request, Sex $sex)
    {
        $gender = $this->sexServices->updateSex($request->validated(), $sex);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($gender)
            ->log('Updated the gender: '.$gender->name);

        return redirect()
            ->route(Auth::user()->getRoleNames()->first().'.sex.index')
            ->with('success', 'You have successfully updated a gender!');
    }

    public function destroy(Sex $sex)
    {
        $gender = $this->sexServices->deleteSex($sex);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($gender)
            ->log('Delted the gender: '.$gender->name);

        return redirect()
            ->route(Auth::user()->getRoleNames()->first().'.sex.index')
            ->with('success', 'You have successfully deleted a gender!');
    }
}
