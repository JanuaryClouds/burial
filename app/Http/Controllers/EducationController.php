<?php

namespace App\Http\Controllers;

use App\DataTables\CmsDataTable;
use App\Http\Requests\EducationRequest;
use App\Models\Education;
use App\Services\DatatableService;
use App\Services\EducationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EducationController extends Controller
{
    protected $educationServices;
    protected $datatableServices;

    public function __construct(EducationService $educationService, DatatableService $datatableService)
    {
        $this->educationServices = $educationService;
        $this->datatableServices = $datatableService;
    }

    public function index()
    {
        $page_title = 'Education';
        $resource = 'education';
        $data = Education::get()
            ->map(function ($education) {
                return [
                    'id' => $education->id,
                    'name' => $education->name,
                    'remarks' => $education->remarks,
                    'show_route' => route('education.edit', $education->id),
                ];
            });
        $columns = $this->datatableServices->getColumns($data, ['id', 'show_route']);
            
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
        $education = Education::findOrFail($id);
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
