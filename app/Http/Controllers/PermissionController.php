<?php

namespace App\Http\Controllers;

use App\DataTables\CmsDataTable;
use App\Http\Requests\PermissionRequest;
use App\Models\Permission;
use App\Services\PermissionService;
use Illuminate\Support\Facades\Auth;

class PermissionController extends Controller
{
    protected $permissionServices;

    public function __construct(PermissionService $permissionServices)
    {
        $this->permissionServices = $permissionServices;
    }

    // Boilerplate Code
    // public function index(CmsDataTable $dataTable)
    // {
    //     $page_title = 'Permission';
    //     $resource = 'permission';
    //     $columns = ['id', 'name', 'guard', 'action'];
    //     $data = Permission::getAllPermissions();

    //     return $dataTable
    //         ->render('cms.index', compact(
    //             'dataTable',
    //             'page_title',
    //             'resource',
    //             'columns',
    //             'data',
    //         ));
    // }

    public function index(CmsDataTable $dataTable)
    {
        $data = Permission::getAllPermissions();
        $resource = 'permission';
        $page_title = 'Permission';

        return view('cms.index', compact('data', 'dataTable', 'resource', 'page_title'));
    }

    public function store(PermissionRequest $request)
    {
        $request['guard_name'] = 'web';
        $permission = $this->permissionServices->storePermission($request->validated());

        activity()
            ->causedBy(Auth::user())
            ->performedOn($permission)
            ->log('Created a new permission: '.$permission->name);

        return redirect()
            ->route(Auth::user()->getRoleNames()->first().'.permission.index')
            ->with('success', 'Permission created successfully.');
    }

    public function update(PermissionRequest $request, Permission $permission)
    {
        $permission = $this->permissionServices->updatePermission($request->validated(), $permission);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($permission)
            ->log('Updated the permission: '.$permission->name);

        return redirect()
            ->route(Auth::user()->getRoleNames()->first().'.permission.index')
            ->with('success', 'Permission updated successfully.');
    }

    public function destroy(Permission $permission)
    {
        $permission = $this->permissionServices->deletePermission($permission);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($permission)
            ->log('Deleted the permission: '.$permission->name);

        return redirect()
            ->route(Auth::user()->getRoleNames()->first().'.permission.index')
            ->with('success', 'Permission deleted successfully.');
    }
}
