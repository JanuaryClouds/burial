<?php

namespace App\Http\Controllers;

use App\DataTables\CmsDataTable;
use App\Http\Requests\NationalityRequest;
use App\Models\Nationality;
use App\Services\NationalityService;
use Illuminate\Http\Request;
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
    
    public function edit(Nationality $nationality)
    {
        $page_title = 'Nationality';
        $type = 'nationality';
        $data = $nationality;
        $resource = 'nationality';
        return view('cms.edit', compact('page_title', 'data', 'type', 'resource'));
    }

    public function store(NationalityRequest $request)
    {
        $nationality = $this->nationalityServices->storeNationality($request->validated());

        activity()
            ->causedBy(Auth::user())
            ->performedOn($nationality)
            ->log('Created a new nationality: '.$nationality->name);

        return redirect()
            ->route('nationality.index')
            ->with('success', 'You have successfully create a nationality!');
    }

    public function update($id, Request $request)
    {
        $nationality = Nationality::findOrFail($id);
        $nationality = $this->nationalityServices->updateNationality($request->validate([
            'name' => 'required',
            'remarks' => 'nullable',
        ]), $nationality);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($nationality)
            ->withProperties(['ip' => request()->ip(), 'browser' => request()->header('User-Agent')])
            ->log('Updated the nationality: '.$nationality->name);

        return redirect()
            ->route('nationality.index')
            ->with('success', 'You have successfully updated a nationality!');
    }

    public function destroy(Nationality $nationality)
    {
        $nationality = $this->nationalityServices->deleteNationality($nationality);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($nationality)
            ->log('Deleted the nationality: '.$nationality->name);

        return redirect()
            ->route('nationality.index')
            ->with('success', 'You have successfully deleted a nationality!');
    }
}
