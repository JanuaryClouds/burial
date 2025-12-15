<?php

namespace App\Http\Controllers;

use App\DataTables\CmsDataTable;
use App\Http\Requests\RoleRequest;
use App\Services\RoleService;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    protected $roleServices;

    public function __construct(RoleService $roleServices)
    {
        $this->roleServices = $roleServices;
    }

    // boilerplate code
    // public function index(CmsDataTable $dataTable)
    // {
    //     $page_title = 'Role';
    //     $resource = 'role';
    //     $columns = ['id', 'name', 'guard', 'action'];
    //     $data = Role::getAllRoles();

    //     return $dataTable
    //         ->render('cms.index', compact(
    //             'dataTable',
    //             'page_title',
    //             'resource',
    //             'columns',
    //             'data',
    //         ));
    // }

    public function index()
    {
        $data = Role::select('id', 'name')->get();
        $page_title = 'Role';
        $resource = 'role';
        $permissions = Permission::select('id', 'name')->get();

        return view('cms.index', compact('data', 'page_title', 'resource', 'permissions'));
    }

    public function edit(Role $role)
    {
        $data = Role::where('id', $role->id)->select('id', 'name')->first();
        $page_title = 'Edit Role';
        $resource = 'role';
        $permissions = Permission::select('id', 'name')->get();

        return view('cms.edit', compact('data', 'page_title', 'resource', 'permissions'));
    }

    public function store(RoleRequest $request)
    {
        $request['guard_name'] = 'web';
        $role = $this->roleServices->storeRole($request->validated());

        if (! $role) {
            return redirect()
                ->back()
                ->with('error', 'Role creation failed.');
        }

        $role->syncPermissions($request->permissions);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($role)
            ->log('Created a new role: '.$role->name);

        return redirect()
            ->back()
            ->with('success', 'Role created successfully.');
    }

    public function update(RoleRequest $request, Role $role)
    {
        $role = $this->roleServices->updateRole($request->validated(), $role);

        $role->syncPermissions($request->permissions);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($role)
            ->log('Updated the role: '.$role->name);

        return redirect()
            ->back()
            ->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        $role = $this->roleServices->deleteRole($role);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($role)
            ->log('Deleted the role: '.$role->name);

        return redirect()
            ->back()
            ->with('success', 'Role deleted successfully.');
    }
}
