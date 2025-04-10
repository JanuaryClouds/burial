<?php

namespace App\Http\Controllers;

use App\DataTables\CmsDataTable;
use App\Models\Religion;
use App\Http\Requests\ReligionRequest;
use App\Services\ReligionService;
use Illuminate\Support\Facades\Auth;

class ReligionController extends Controller
{
    protected $religionServices;
    
    public function __construct(ReligionService $religionServices)
    {
        $this->religionService = $religionServices;
    }
    
    public function index(CmsDataTable $dataTable)
    {
        $page_title = 'Religion';
        $resource = 'religion';
        $columns = ['name', 'remarks'];
        $data = Religion::getAllReligions();
        
        return $dataTable
            ->render('cms.index', compact(
                'dataTable',
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
            ->log('Created a new religion: ' . $religion->name);

        return redirect()
            ->route(Auth::user()->getRoleNames()->first() . '.religion.index')
            ->with('success', 'You have successfully created a religion!');
    }
    
    public function update(ReligionRequest $request, Religion $religion)
    {
        $religion = $this->religionServices->updateReligion($request->validated(), $religion);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($religion)
            ->log('Created the religion: ' . $religion->name);
        
        return redirect()
            ->route(Auth::user()->getRoleNames()->first() . '.religion.index')
            ->with('sucess', 'You have successfully updated a religion!');
    }
    
    public function destroy(Religion $religion)
    {
        $religion = $this->religionServices->deleteReligion($religion);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($religion)
            ->log('Created the religion: ' . $religion->name);

        return redirect()
            ->route(Auth::user()->getRoleNames()->first() . '.religion.index')
            ->with('success', 'You have successfully deleted a religion!');
    }
}