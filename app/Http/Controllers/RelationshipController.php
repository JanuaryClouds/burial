<?php

namespace App\Http\Controllers;

use App\DataTables\CmsDataTable;
use App\Http\Requests\RelationshipRequest;
use App\Models\Relationship;
use App\Services\RelationshipService;
use Illuminate\Support\Facades\Auth;

class RelationshipController extends Controller
{
    protected $relationshipServices;

    public function __construct(RelationshipService $relationshipServices)
    {
        $this->relationshipServices = $relationshipServices;
    }

    public function index(CmsDataTable $dataTable)
    {
        $page_title = 'Relationship';
        $resource = 'relationship';
        $columns = ['id', 'name', 'remarks', 'action'];
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
            ->log('Created a new relationship: '.$relationship->name);

        return redirect()
            ->back()
            ->with('success', 'Relationship created successfully.');
    }

    public function edit(Relationship $relationship)
    {
        $page_title = 'Relationship';
        $resource = 'relationship';
        $data = $relationship;

        return view('cms.edit', compact('data', 'page_title', 'resource'));
    }

    public function update(RelationshipRequest $request, Relationship $relationship)
    {
        try {
            $relationship = $this->relationshipServices->updateRelationship($request->validated(), $relationship);
            activity()
                ->causedBy(Auth::user())
                ->performedOn($relationship)
                ->withProperties(['ip' => request()->ip(), 'browser' => request()->header('User-Agent')])
                ->log('Updated the relationship: '.$relationship->name);
    
            return redirect()
                ->route('relationship.index')
                ->with('success', 'Relationship updated successfully.');
        } catch (\Throwable $th) {
            return redirect()
                ->back()
                ->with('error', 'Unable to update relationship. ' . config('app.env') === 'local' ? $th->getMessage() : '');
        }
    }

    public function destroy(Relationship $relationship)
    {
        $relationship = $this->relationshipServices->deleteRelationship($relationship);
        activity()
            ->causedBy(Auth::user())
            ->performedOn($relationship)
            ->log('Deleted the relationship: '.$relationship->name);

        return redirect()
            ->route('relationship.index')
            ->with('success', 'Relationship deleted successfully.');
    }
}
