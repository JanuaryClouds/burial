<?php

namespace App\Http\Controllers;

use App\DataTables\CmsDataTable;
use App\Http\Requests\BarangayRequest;
use App\Models\Barangay;
use App\Models\District;
use App\Services\BarangayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

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

    public function show(Barangay $barangay)
    {
        $page_title = 'Barangay';
        $type = 'barangay';
        $data = $barangay;
        // return view('cms.show', compact('page_title', 'data', 'type'));
    }

    public function edit($id)
    {
        $page_title = 'Barangay';
        $resource = 'barangay';
        $data = Barangay::select('id', 'name')->findOrFail($id);

        return view('cms.edit', compact('page_title', 'data', 'resource'));
    }

    public function store(BarangayRequest $request)
    {
        $barangay = $this->BarangayServices->storeBarangay($request->validated());

        activity()
            ->causedBy(Auth::user())
            ->performedOn($barangay)
            ->log('Created a new barangay: '.$barangay->name);

        return redirect()
            ->route('barangay.index')
            ->with('success', 'Barangay Created Successfully');
    }

    public function update(Request $request, Barangay $Barangay)
    {
        $barangay = $this->BarangayServices->updateBarangay($request->validate([
            'name' => 'required',
            'remarks' => 'nullable',
            ]), $Barangay);
            
        activity()
            ->causedBy(Auth::user())
            ->performedOn($barangay)
            ->withProperties(['ip' => request()->ip(), 'browser' => request()->header('User-Agent')])
            ->log('Updated the barangay: '.$barangay->name);

        return redirect()
            ->route('barangay.index')
            ->with('success', 'Barangay Updated Successfully');
    }

    public function destroy(Barangay $Barangay)
    {
        $barangay = $this->BarangayServices->deleteBarangay($Barangay);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($barangay)
            ->log('Deleted the barangay: '.$barangay->name);

        return redirect()
            ->route('barangay.index')
            ->with('success', 'Barangay Deleted Successfully');
    }
}
