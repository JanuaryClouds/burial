<?php

namespace App\Http\Controllers;

use App\DataTables\CmsDataTable;
use App\Http\Requests\PermissionRequest;
use App\Models\Permission;
use App\Services\PermissionService;
use Illuminate\Support\Facades\Auth;

class PermissionController extends Controller
{
    public function __construct(
        protected PermissionService $permissionServices
    ) {}

    // Boilerplate Code
    // public function index(CmsDataTable $dataTable)
    // {
    //     $pageTitle = 'Permission';
    //     $resource = 'permission';
    //     $columns = ['id', 'name', 'guard', 'action'];
    //     $data = Permission::getAllPermissions();

    //     return $dataTable
    //         ->render('cms.index', compact(
    //             'dataTable',
    //             'pageTitle',
    //             'resource',
    //             'columns',
    //             'data',
    //         ));
    // }

    // public function index()
    // {
    //     $data = Permission::getAllPermissions()
    //         ->map(function ($permission) {
    //             return [
    //                 'id' => $permission->id,
    //                 'name' => $permission->name,
    //                 'guard' => $permission->guard_name,
    //             ];
    //         });
    //     $columns = ['name', 'guard'];
    //     $resource = 'permission';
    //     $pageTitle = 'Permission';

    //     if (request()->expectsJson()) {
    //         return response()->json([
    //             'data' => $data->values(),
    //         ]);
    //     }

    //     return view('cms.index', compact('data', 'resource', 'columns', 'pageTitle'));
    // }

    // public function store(PermissionRequest $request)
    // {
    //     $request['guard_name'] = 'web';
    //     $permission = $this->permissionServices->storePermission($request->validated());

    //     activity()
    //         ->causedBy(Auth::user())
    //         ->performedOn($permission)
    //         ->log('Created a new permission: '.$permission->name);

    //     return redirect()
    //         ->route('permission.index')
    //         ->with('success', 'Permission created successfully.');
    // }

    // public function update(PermissionRequest $request, Permission $permission)
    // {
    //     $permission = $this->permissionServices->updatePermission($request->validated(), $permission);

    //     activity()
    //         ->causedBy(Auth::user())
    //         ->performedOn($permission)
    //         ->log('Updated the permission: '.$permission->name);

    //     return redirect()
    //         ->route('permission.index')
    //         ->with('success', 'Permission updated successfully.');
    // }

    // public function destroy(Permission $permission)
    // {
    //     $permission = $this->permissionServices->deletePermission($permission);

    //     activity()
    //         ->causedBy(Auth::user())
    //         ->performedOn($permission)
    //         ->log('Deleted the permission: '.$permission->name);

    //     return redirect()
    //         ->route('permission.index')
    //         ->with('success', 'Permission deleted successfully.');
    // }
}
