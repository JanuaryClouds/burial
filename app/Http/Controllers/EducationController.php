<?php

namespace App\Http\Controllers;

use App\Models\Education;
use App\DataTables\CmsDataTable;
use App\Services\EducationService;
use App\Http\Requests\EducationRequest;
use Illuminate\Support\Facades\Auth;

class EducationController extends Controller
{
    protected $educationService;
    
    public function __construct(EducationService $educationService)
    {
        $this->educationService = $educationService;
    }
    
    public function index(CmsDataTable $dataTable)
    {
        $page_title = 'Education';
        $resource = 'education';
        $column = ['name', 'remarks'];
        $data = Education::getAllEducations();

        return $dataTable
            ->render('cms.index', compact(
                'dataTable',
                'page_title',
                'resource',
                'column',
                'data'
            ));
    }
    
    public function store(EducationRequest $request)
    {
        $education = $this->educationService->storeEducation($request->validated());

        activity()
            ->performedOn($education)
            ->causedBy(Auth::user())
            ->log('Created Education: ' . $education->name);

        return redirect()
            ->route(Auth::user()->getRoleNames()->first() . '.education.index')
            ->with('success', 'Education created successfully.');
    }
    
    public function update(EducationRequest $request, Education $education)
    {
        $education = $this->educationService->updateEducation($request->validated(), $education);

        activity()
            ->performedOn($education)
            ->causedBy(Auth::user())
            ->log('Updated Education: ' . $education->name);
            
        return redirect()
            ->route(Auth::user()->getRoleNames()->first() . '.education.index')
            ->with('success', 'Education updated successfully.');
    }
    
    public function destroy(Education $education)
    {
        $education = $this->educationService->deleteEducation($education);

        activity()
            ->performedOn($education)
            ->causedBy(Auth::user())
            ->log('Deleted Education: ' . $education->name);
            
        return redirect()
            ->route(Auth::user()->getRoleNames()->first() . '.education.index')
            ->with('success', 'Education deleted successfully.');
    }
}