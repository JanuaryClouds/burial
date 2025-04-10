<?php

namespace App\Http\Controllers;

use App\Models\Relationship;
use App\Http\Requests\RelationshipRequest;
use App\DataTables\CmsDataTable;
use App\Services\RelationshipService;
use Illuminate\Support\Facades\Auth;

class RelationshipController extends Controller
{
    protected $relationshipServices;
    
    public function __construct(RelationshipService $relationshipServices)
    {
        $this->relationshipService = $relationshipServices;
    }
    
    public function index(CmsDataTable $dataTable)
    {
        $page_title = 'Relationship';
        $resource = 'relationship';
        $columns = ['name', 'remarks'];
        $data = Relationship::getAllRelationships();

        return $dataTable
            ->render('cms.index', compact(
                'dataTable',
                'page_title',
                'resource',
                'columns',
                'data'
            ));
    }
    
    public function store(RelationshipRequest $request)
    {
        $relationship = $this->relationshipServices->storeRelationship($request->validated());

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
        $relationship = $this->relationshipServices->updateRelationship($request->validated(), $relationship);

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
        $relationship = $this->relationshipServices->deleteRelationship($relationship);
        activity()
            ->causedBy(Auth::user())
            ->performedOn($relationship)
            ->log('Deleted the relationship: ' . $relationship->name);
        return redirect()
            ->route(Auth::user()->getRoleNames()->first() . '.relationship.index')
            ->with('success', 'Relationship deleted successfully.');
    }
}