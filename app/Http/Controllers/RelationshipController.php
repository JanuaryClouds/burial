<?php

namespace App\Http\Controllers;

use App\Http\Requests\RelationshipRequest;
use App\Models\Relationship;
use App\Services\DatatableService;
use App\Services\RelationshipService;
use Illuminate\Support\Facades\Auth;

class RelationshipController extends Controller
{
    protected $relationshipServices;

    protected $datatableServices;

    public function __construct(RelationshipService $relationshipServices, DatatableService $datatableService)
    {
        $this->relationshipServices = $relationshipServices;
        $this->datatableServices = $datatableService;
    }

    public function index()
    {
        $page_title = 'Relationship';
        $resource = 'relationship';
        $data = Relationship::withTrashed()
            ->get()
            ->map(function ($relationship) {
                return [
                    'id' => $relationship->id,
                    'name' => $relationship->name.($relationship->deleted_at ? ' (disabled)' : ''),
                    'remarks' => $relationship->remarks,
                    'show_route' => route('relationship.edit', $relationship->id),
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

    public function edit($id)
    {
        $page_title = 'Relationship';
        $resource = 'relationship';
        $data = Relationship::withTrashed()->findOrFail($id);

        return view('cms.edit', compact('data', 'page_title', 'resource'));
    }

    public function update(RelationshipRequest $request, $id)
    {
        try {
            $data = $request->validated();
            $relationship = Relationship::withTrashed()->findOrFail($id);
            $this->relationshipServices->updateRelationship($data, $relationship);

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
                ->with('error', 'Unable to update relationship. '.config('app.env') === 'local' ? $th->getMessage() : '');
        }
    }

    public function destroy($id)
    {
        $relationship = Relationship::withTrashed()->findOrFail($id);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($relationship)
            ->log('Deleted the relationship: '.$relationship->name);

        $this->relationshipServices->deleteRelationship($relationship);

        return redirect()
            ->route('relationship.index')
            ->with('success', 'Relationship soft deleted successfully.');
    }

    public function restore($id)
    {
        $relationship = Relationship::withTrashed()->findOrFail($id);
        $this->relationshipServices->restoreRelationship($relationship);

        activity()
            ->performedOn($relationship)
            ->causedBy(Auth::user())
            ->withProperties(['ip' => request()->ip(), 'browser' => request()->header('User-Agent')])
            ->log('Restored the relationship: '.$relationship->name);

        return redirect()
            ->route('relationship.index')
            ->with('success', 'Relationship restored successfully.');
    }
}
