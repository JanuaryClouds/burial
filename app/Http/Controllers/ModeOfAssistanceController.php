<?php

namespace App\Http\Controllers;

use App\DataTables\CmsDataTable;
use App\Http\Requests\ModeOfAssistanceRequest;
use App\Models\ModeOfAssistance;
use App\Services\ModeOfAssistanceService;
use Illuminate\Support\Facades\Auth;

class ModeOfAssistanceController extends Controller
{
    protected $ModeOfAssistanceServices;

    public function __construct(ModeOfAssistanceService $ModeOfAssistanceServices)
    {
        $this->ModeOfAssistanceServices = $ModeOfAssistanceServices;
    }

    public function index(CmsDataTable $dataTable)
    {
        $page_title = 'Mode of assistance';
        $resource = 'moa';
        $columns = ['id', 'name', 'remarks', 'action'];
        $data = ModeOfAssistance::getAllMoas();

        return $dataTable
            ->render('cms.index', compact(
                'dataTable',
                'page_title',
                'resource',
                'columns',
                'data'
            ));
    }

    public function store(ModeOfAssistanceRequest $request)
    {
        $moa = $this->ModeOfAssistanceServices->storeModeOfAssistance($request->validated());

        activity()
            ->performedOn($moa)
            ->causedBy(Auth::user())
            ->log('Created a new assistance: '.$moa->name);

        return redirect()
            ->route(Auth::user()->getRoleNames()->first().'.moa.index')
            ->with('success', 'Mode of assistance created successfully.');
    }

    public function update(ModeOfAssistanceRequest $request, ModeOfAssistance $moa)
    {
        $moa = $this->ModeOfAssistanceServices->updateModeOfAssistance($request->validated(), $moa);

        activity()
            ->performedOn($moa)
            ->causedBy(Auth::user())
            ->log('Updated the Mode of assistance: '.$moa->name);

        return redirect()
            ->route(Auth::user()->getRoleNames()->first().'.moa.index')
            ->with('success', 'Mode of assistance updated successfully.');
    }

    public function destroy(ModeOfAssistance $moa)
    {
        $moa = $this->ModeOfAssistanceServices->deleteModeOfAssistance($moa);

        activity()
            ->performedOn($moa)
            ->causedBy(Auth::user())
            ->log('Deleted the Mode of assistance: '.$moa->name);

        return redirect()
            ->route(Auth::user()->getRoleNames()->first().'.moa.index')
            ->with('success', 'Mode of assistance deleted successfully.');
    }
}
