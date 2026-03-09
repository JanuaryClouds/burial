<?php

namespace App\Http\Controllers;

use App\DataTables\CmsDataTable;
use App\Http\Requests\DistrictRequest;
use App\Models\District;
use App\Services\DistrictService;
use Illuminate\Support\Facades\Auth;

class DistrictController extends Controller
{
    protected $districtServices;

    public function __construct(DistrictService $districtServices)
    {
        $this->districtServices = $districtServices;
    }

    public function index()
    {
        $page_title = 'District';
        $resource = 'district';
        $columns = ['id', 'name', 'remarks', 'action'];
        $data = District::getAllDistricts()
            ->map(function ($district) {
                return [
                    'id' => $district->id,
                    'name' => $district->name,
                    'remarks' => $district->remarks,
                    'action' => $district->action,
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

    public function store(DistrictRequest $request)
    {
        $district = $this->districtServices->storeDistrict($request->validated());

        activity()
            ->performedOn($district)
            ->causedBy(Auth::user())
            ->log('District created:'.$district->name);

        return redirect()
            ->route(Auth::user()->getRoleNames()->first().'.district.index')
            ->with('success', 'District created successfully!');
    }

    public function update(DistrictRequest $request, District $district)
    {
        $district = $this->districtServices->updateDistrict($request->validated(), $district);

        activity()
            ->performedOn($district)
            ->causedBy(Auth::user())
            ->log('District updated:'.$district->name);

        return redirect()
            ->route(Auth::user()->getRoleNames()->first().'.district.index')
            ->with('success', 'District updated successfully!');
    }

    public function destroy(District $district)
    {
        $district = $this->districtServices->deleteDistrict($district);

        activity()
            ->performedOn($district)
            ->causedBy(Auth::user())
            ->log('District deleted:'.$district->name);

        return redirect()
            ->route(Auth::user()->getRoleNames()->first().'.district.index')
            ->with('success', 'District deleted successfully!');
    }
}
