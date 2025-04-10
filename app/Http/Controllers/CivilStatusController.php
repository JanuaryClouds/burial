<?php

namespace App\Http\Controllers;

use App\Models\CivilStatus;
use App\DataTables\CmsDataTable;
use App\Services\CivilStatusService;
use App\Http\Requests\CivilStatusRequest;
use Illuminate\Support\Facades\Auth;

class CivilStatusController extends Controller
{
    protected $civilStatusServices;
    public function __construct(CivilStatusService $civilStatusServices)
    {
        $this->civilStatusService = $civilStatusServices;
    }
    
    public function index(CmsDataTable $dataTable)
    {
        $page_title = 'Civil Status';
        $resource = 'civil';
        $columns = ['id', 'name', 'remarks', 'action'];
        $data = CivilStatus::getAllCivilStatuses();

        return $dataTable
            ->render('cms.index', compact(
                'page_title',
                'resource',
                'columns',
                'data',
                'dataTable'
            ));
    }
    
    public function store(CivilStatusRequest $request)
    {
        $civil = $this->civilStatusServices->storeCivilStatus($request->validated());

        activity()
        ->causedBy(Auth::user())
            ->performedOn($civil)
            ->log('Created a new civil status: ' . $civil->name);

        return redirect()
            ->route(Auth::user()->getRoleNames()->first() . '.civil.index')
            ->with('success', 'Civil Status Created Successfully');
    }
    
    public function update(CivilStatusRequest $request, CivilStatus $civilStatus)
    {
        $civil = $this->civilStatusServices->updateCivilStatus($request->validated(), $civilStatus);
        
        activity()
            ->causedBy(Auth::user())
            ->performedOn($civil)
            ->log('Updated the civil status: ' . $civil->name);

        return redirect()
            ->route(Auth::user()->getRoleNames()->first() . '.civil.index')
            ->with('success', 'Civil Status Updated Successfully');
    }
    
    public function destroy(CivilStatus $civilStatus)
    {
        $civil = $this->civilStatusServices->deleteCivilStatus($civilStatus);
        
        activity()
            ->causedBy(Auth::user())
            ->performedOn($civil)
            ->log('Deleted the civil status: ' . $civil->name);

        return redirect()
            ->route(Auth::user()->getRoleNames()->first() . '.civil.index')
            ->with('success', 'Civil Status Deleted Successfully');
    }
}