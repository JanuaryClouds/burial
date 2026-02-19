<?php

namespace App\Http\Controllers;

use App\DataTables\CmsDataTable;
use App\Http\Requests\EducationRequest;
use App\Models\Education;
use App\Services\EducationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EducationController extends Controller
{
    protected $educationServices;

    public function __construct(EducationService $educationServices)
    {
        $this->educationServices = $educationServices;
    }

    public function index(CmsDataTable $dataTable)
    {
        $page_title = 'Education';
        $resource = 'education';
        $columns = ['id', 'name', 'remarks', 'action'];
        $data = Education::getAllEducations();

        return $dataTable
            ->render('cms.index', compact(
                'dataTable',
                'page_title',
                'resource',
                'columns',
                'data'
            ));
    }

    public function edit(Education $education)
    {
        $page_title = 'Education';
        $resource = 'education';
        $data = $education;

        return view('cms.edit', compact(
            'data',
            'resource',
            'page_title',
        ));
    }

    public function store(EducationRequest $request)
    {
        $education = $this->educationServices->storeEducation($request->validated());

        activity()
            ->performedOn($education)
            ->causedBy(Auth::user())
            ->log('Created a new education: '.$education->name);

        return redirect()
            ->route('education.index')
            ->with('success', 'Education created successfully.');
    }

    public function update($id, Request $request)
    {
        try {
            $education = Education::find($id);
            $education = $this->educationServices->updateEducation($request->validate([
                'name' => 'required',
                'remarks' => 'nullable'
            ]), $education);
    
            activity()
                ->performedOn($education)
                ->causedBy(Auth::user())
                ->withProperties(['ip' => request()->ip(), 'browser' => request()->header('User-Agent')])
                ->log('Updated the education: '.$education->name);
    
            return redirect()
                ->route('education.index')
                ->with('success', 'Education updated successfully.');
        } catch (\Throwable $th) {
            return back()->with('error', 'Unable to update education ' . config('app.env') == 'local' ? $th->getMessage() : '');
        }
    }

    public function destroy(Education $education)
    {
        $education = $this->educationServices->deleteEducation($education);

        activity()
            ->performedOn($education)
            ->causedBy(Auth::user())
            ->log('Deleted the education: '.$education->name);

        return redirect()
            ->route('education.index')
            ->with('success', 'Education deleted successfully.');
    }
}
