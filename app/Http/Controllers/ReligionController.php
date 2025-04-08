<?php

namespace App\Http\Controllers;

use App\DataTables\CmsDataTable;
use App\Models\Religion;
use App\Http\Requests\ReligionRequest;
use App\Services\ReligionService;
use Illuminate\Support\Facades\Auth;

class ReligionController extends Controller
{
    protected $religionService;
    
    public function __construct(ReligionService $religionService)
    {
        $this->religionService = $religionService;
    }
    
    public function index(CmsDataTable $dataTable)
    {
        $page_title = 'Religion';
        $resource = 'religion';
        $column = ['name', 'remarks'];
        $data = Religion::getAllReligions();
        
        return $dataTable
            ->render('cms.index', compact(
                'dataTable',
                'page_title',
                'resource',
                'column',
                'data',
            ));
    }
    
    public function store(ReligionRequest $request)
    {
        $religion = $this->religionService->storeReligion($request->validated());

        activity()
            ->causedBy(Auth::user())
            ->performedOn($religion)
            ->log('religion created');

        return redirect()
            ->route(Auth::user()->getRoleNames()->first() . '.religion.index')
            ->with('success', 'You have successfully created a religion!');
    }
    
    public function update(ReligionRequest $request, Religion $religion)
    {
        $religion = $this->religionService->updateReligion($request->validated(), $religion);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($religion)
            ->log('religion updated');
        
        return redirect()
            ->route(Auth::user()->getRoleNames()->first() . '.religion.index')
            ->with('sucess', 'You have successfully updated a religion!');
    }
    
    public function destroy(Religion $religion)
    {
        $religion = $this->religionService->deleteReligion($religion);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($religion)
            ->log('religion deleted');

        return redirect()
            ->route(Auth::user()->getRoleNames()->first() . '.religion.index')
            ->with('success', 'You have successfully deleted a religion!');
    }
}