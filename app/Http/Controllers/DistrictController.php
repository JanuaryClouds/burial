<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Services\DistrictService; 
use App\DataTables\CmsDataTable;
use App\Http\Requests\DistrictRequest;
use Illuminate\Support\Facades\Auth;

class DistrictController extends Controller
{
    protected $districtServices;
    
    public function __construct(DistrictService $districtServices)
    {
        $this->districtServices = $districtServices;
    }
    
    public function index(CmsDataTable $dataTable)
    {
        $page_title = 'District';
        $resource = 'district';
        $columns = ['id', 'name', 'remarks', 'action'];
        $data = District::getAllDistricts();

        return $dataTable
            ->render('cms.index', compact(
                'dataTable',
                'page_title',
                'resource',
                'columns',
                'data'
            ));
    }
    
    public function store(Request $request)
    {
        $district = $this->districtServices->storeDistrict($request->all());
        
        activity()
            ->performedOn($district)
            ->causedBy(Auth::user())
            ->log('District created:' . $district->name);

        return redirect()
            ->route(Auth::user()->getRoleNames()->first() . '.district.index')
            ->with('success', 'District created successfully!');
    }
    
    public function update(Request $request, District $district)
    {
        $district = $this->districtServices->updateDistrict($request->validated(), $district);

        activity()
            ->performedOn($district)
            ->causedBy(Auth::user())
            ->log('District updated:'. $district->name);

        return redirect()
            ->route(Auth::user()->getRoleNames()->first() . '.district.index')
            ->with('success', 'District updated successfully!');
    }
    
    public function destroy(District $district)
    {
        $district = $this->districtServices->deleteDistrict($district);

        activity()
            ->performedOn($district)
            ->causedBy(Auth::user())
            ->log('District deleted:'. $district->name);

        return redirect()
            ->route(Auth::user()->getRoleNames()->first() . '.district.index')
            ->with('success', 'District deleted successfully!');
    }
}