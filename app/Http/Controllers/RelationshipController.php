<?php

namespace App\Http\Controllers;

use App\Models\Relationship;
use App\Http\Requests\RelationshipRequest;
use App\DataTables\CmsDataTable;
use App\Services\RelationshipService;
use Illuminate\Support\Facades\Auth;

class RelationshipController extends Controller
{
    protected $relationshipService;
    
    public function __construct(RelationshipService $relationshipService)
    {
        $this->relationshipService = $relationshipService;
    }
    
    public function index(CmsDataTable $dataTable)
    {
        $page_title = 'Relationship';
        $resource = 'relationship';
        $column = ['name', 'remarks'];
        $data = Relationship::getAllRelationships();

        return $dataTable
            ->render('cms.index', compact(
                'dataTable',
                'page_title',
                'resource',
                'column',
                'data'
            ));
    }
    
    public function store(RelationshipRequest $request)
    {
        $relationship = $this->relationshipService->storeRelationship($request->validated());

        activity()
            ->causedBy(Auth::user())
            ->performedOn($relationship)
            ->log('Created a new relationship: ' . $relationship->name);

        return redirect()
            ->route(Auth::user()->getRoleNames()->first() . '.relationship.index')
            ->with('success', 'Relationship created successfully.');
    }

    public function update(RelationshipRequest $request, Relationship $relationship)
    {
        $relationship = $this->relationshipService->updateRelationship($request->validated(), $relationship);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($relationship)
            ->log('Updated the relationship: ' . $relationship->name);
            
        return redirect()
            ->route(Auth::user()->getRoleNames()->first() . '.relationship.index')
            ->with('success', 'Relationship updated successfully.');
    }
    
    public function destroy(Relationship $relationship)
    {
        $relationship = $this->relationshipService->deleteRelationship($relationship);
        activity()
            ->causedBy(Auth::user())
            ->performedOn($relationship)
            ->log('Deleted the relationship: ' . $relationship->name);
        return redirect()
            ->route(Auth::user()->getRoleNames()->first() . '.relationship.index')
            ->with('success', 'Relationship deleted successfully.');
    }
}