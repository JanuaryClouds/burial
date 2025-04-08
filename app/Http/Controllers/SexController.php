<?php

namespace App\Http\Controllers;

use App\Models\Sex;
use App\Http\Requests\SexRequest;
use App\Services\SexService;
use App\DataTables\CmsDataTable;
use Illuminate\Support\Facades\Auth;

class SexController extends Controller
{
    protected $sexService;
    
    public function __construct(SexService $sexService)
    {
        $this->sexService = $sexService;
    }
    
    public function index(CmsDataTable $dataTable)
    {
        $page_title = 'Sex';
        $resource = 'sex';
        $column = ['id', 'name', 'remarks'];
        $data = Sex::getAllSexes();

        return $dataTable
            ->render('cms.index', compact(
                'dataTable',
                'page_title',
                'resource',
                'column',
                'data'
            ));
    }
    
    public function store(SexRequest $request)
    {
        $gender = $this->sexService->storeSex($request->validated());
        activity()
            ->causedBy(Auth::user())
            ->performedOn($gender)
            ->log('Created a new gender: ' . $gender->name);

        return redirect()
            ->route(Auth::user()->getRoleNames()->first() . '.sex.index')
            ->with('success', 'You have successfully created a gender!');
    }

    public function update(SexRequest $request, Sex $sex)
    {
        $gender = $this->sexService->updateSex($request->validated(), $sex);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($gender)
            ->log('Updated the gender: ' . $gender->name);

        return redirect()
            ->route(Auth::user()->getRoleNames()->first() . '.sex.index')
            ->with('success', 'You have successfully updated a gender!');
    }
    
    public function destroy(Sex $sex)
    {
        $gender = $this->sexService->deleteSex($sex);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($gender)
            ->log('Delted the gender: ' . $gender->name);

        return redirect()
            ->route(Auth::user()->getRoleNames()->first() . '.sex.index')
            ->with('success','You have successfully deleted a gender!');
    }
}