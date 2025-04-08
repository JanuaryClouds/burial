<?php

namespace App\Http\Controllers;

use App\Models\Nationality;
use App\Http\Requests\NationalityRequest;
use App\DataTables\CmsDataTable;
use App\Services\NationalityService;
use Illuminate\Support\Facades\Auth;

class NationalityController extends Controller
{
    protected $nationalityService;

    public function __construct(NationalityService $nationalityService)
    {
        $this->nationalityService = $nationalityService;
    }
    
    public function index(CmsDataTable $dataTable)
    {
        $page_title = 'Nationality';
        $resource = 'nationality';
        $column = ['name', 'remarks'];
        $data = Nationality::getAllNationalities();

        return $dataTable
            ->render('cms.index', compact(
                'dataTable',
                'page_title',
                'resource',
                'column',
                'data'
            ));
    }
    
    public function store(NationalityRequest $request)
    {
        $nationality = $this->nationalityService->storeNationality($request->validated());

        activity()
            ->causedBy(Auth::user())
            ->performedOn($nationality)
            ->log('nationality created');

        return redirect()
            ->route(Auth::user()->getRoleNames()->first() . 'nationality.index')
            ->with('success', 'You have successfully create a nationality!');
    }
    
    public function update(NationalityRequest $request, Nationality $nationality)
    {
        $nationality = $this->nationalityService->updateNationality($request->validated(), $nationality);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($nationality)
            ->log('nationality updated');

        return redirect()
            ->route(Auth::user()->getRoleNames()->first() . 'nationality.index')
            ->with('success', 'You have successfully updated a nationality!');
    }
    
    public function destroy(Nationality $nationality)
    {
        $nationality = $this->nationalityService->deleteNationality($nationality);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($nationality)
            ->log('nationality deleted');

        return redirect()
            ->route(Auth::user()->gettRoleNames()->first() . '.nationality.index')
            ->with('success', 'You have successfully deleted a nationality!');
    }
}