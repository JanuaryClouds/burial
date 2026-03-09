<?php

namespace App\Http\Controllers;

use App\DataTables\CmsDataTable;
use App\Http\Requests\BarangayRequest;
use App\Models\Barangay;
use App\Models\District;
use App\Services\BarangayService;
use App\Services\DatatableService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

class BarangayController extends Controller
{
    protected $BarangayServices;
    protected $datatableServices;

    public function __construct(BarangayService $BarangayServices, DatatableService $datatableService)
    {
        $this->BarangayServices = $BarangayServices;
        $this->datatableServices = $datatableService;
    }

    public function index()
    {
        $page_title = 'Barangay';
        $resource = 'barangay';
        $data = Barangay::get()->map(function ($barangay) {
            return [
                'id' => $barangay->id,
                'name' => $barangay->name,
                'district' => $barangay->district?->name ?? '',
                'remarks' => $barangay->remarks,
                'show_route' => route('barangay.edit', $barangay->id),
            ];
        });
        $columns = $this->datatableServices->getColumns($data, ['id']);
        // $subRecords = District::getAllDistricts();
        
        if (request()->expectsJson()) {
            return response()->json([
                'data' => $data->values(),
            ]);
        }

        return view('cms.index', compact(
                'page_title',
                'resource',
                'columns',
                'data',
                // 'subRecords'
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
