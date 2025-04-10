<?php

namespace App\Http\Controllers;

use App\Models\Nationality;
use App\Http\Requests\NationalityRequest;
use App\DataTables\CmsDataTable;
use App\Services\NationalityService;
use Illuminate\Support\Facades\Auth;

class NationalityController extends Controller
{
    protected $nationalityServices;

    public function __construct(NationalityService $nationalityServices)
    {
        $this->nationalityServices = $nationalityServices;
    }
    
    public function index(CmsDataTable $dataTable)
    {
        $page_title = 'Nationality';
        $resource = 'nationality';
        $columns = ['id', 'name', 'remarks', 'action'];
        $data = Nationality::getAllNationalities();

        return $dataTable
            ->render('cms.index', compact(
                'dataTable',
                'page_title',
                'resource',
                'columns',
                'data'
            ));
    }
    
    public function store(NationalityRequest $request)
    {
        $nationality = $this->nationalityServices->storeNationality($request->validated());

        activity()
            ->causedBy(Auth::user())
            ->performedOn($nationality)
            ->log('Created a new nationality: ' . $nationality->name);

        return redirect()
            ->route(Auth::user()->getRoleNames()->first() . 'nationality.index')
            ->with('success', 'You have successfully create a nationality!');
    }
    
    public function update(NationalityRequest $request, Nationality $nationality)
    {
        $nationality = $this->nationalityServices->updateNationality($request->validated(), $nationality);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($nationality)
            ->log('Updatred the nationality: ' . $nationality->name);

        return redirect()
            ->route(Auth::user()->getRoleNames()->first() . 'nationality.index')
            ->with('success', 'You have successfully updated a nationality!');
    }
    
    public function destroy(Nationality $nationality)
    {
        $nationality = $this->nationalityServices->deleteNationality($nationality);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($nationality)
            ->log('Deleted the nationality: ' . $nationality->name);

        return redirect()
            ->route(Auth::user()->gettRoleNames()->first() . '.nationality.index')
            ->with('success', 'You have successfully deleted a nationality!');
    }
}