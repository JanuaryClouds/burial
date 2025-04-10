<?php

namespace App\Http\Controllers;

use App\Models\Barangay;
use App\Models\District;
use App\DataTables\CmsDataTable;
use App\Services\BarangayService;
use App\Http\Requests\BarangayRequest;
use Illuminate\Support\Facades\Auth;

class BarangayController extends Controller
{
    protected $BarangayServices;
    
    public function __construct(BarangayService $BarangayServices)
    {
        $this->BarangayServices = $BarangayServices;
    }
    
    public function index(CmsDataTable $dataTable)
    {
        $page_title = 'Barangay';
        $resource = 'barangay';
        $columns = ['id', 'name', 'remarks', 'action'];
        $data = Barangay::getAllBarangays();
        $subRecords = District::getAllDistricts();

        return $dataTable
            ->render('cms.index', compact(
                'page_title',
                'resource',
                'columns',
                'data',
                'dataTable',
                'subRecords'
            ));
    }
    
    public function store(BarangayRequest $request)
    {
        $barangay = $this->BarangayServices->storeBarangay($request->validated());

        activity()
        ->causedBy(Auth::user())
            ->performedOn($barangay)
            ->log('Created a new barangay: ' . $barangay->name);

        return redirect()
            ->route(Auth::user()->getRoleNames()->first() . '.barangay.index')
            ->with('success', 'Barangay Created Successfully');
    }
    
    public function update(BarangayRequest $request, Barangay $Barangay)
    {
        $barangay = $this->BarangayServices->updateBarangay($request->validated(), $Barangay);
        
        activity()
            ->causedBy(Auth::user())
            ->performedOn($barangay)
            ->log('Updated the barangay: ' . $barangay->name);

        return redirect()
            ->route(Auth::user()->getRoleNames()->first() . '.barangay.index')
            ->with('success', 'Barangay Updated Successfully');
    }
    
    public function destroy(Barangay $Barangay)
    {
        $barangay = $this->BarangayServices->deleteBarangay($Barangay);
        
        activity()
            ->causedBy(Auth::user())
            ->performedOn($barangay)
            ->log('Deleted the barangay: ' . $barangay->name);

        return redirect()
            ->route(Auth::user()->getRoleNames()->first() . '.barangay.index')
            ->with('success', 'Barangay Deleted Successfully');
    }
}